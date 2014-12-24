<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Holds the logic for attachments manipulation
 */
abstract class JemAttachment {
	/**
	 * upload files for the specified object
	 *
	 * @param $post_files	array data from JInput 'files' + form fields
	 * @param $object		object identification (should be event<eventid>, etc...)
	 */
	static function postUpload($post_files, $object) {
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		require_once JPATH_SITE.'/components/com_jem/classes/image.class.php';

		$user = JFactory::getUser();
		$jemsettings = JemHelper::config();

		$path = JPATH_SITE.'/'.$jemsettings->attachments_path.'/'.$object;

		if (!(is_array($post_files) && count($post_files))) {
			return false;
		}

		$allowed = explode(",", $jemsettings->attachments_types);
		foreach ($allowed as $k => $v) {
			$allowed[$k] = trim($v);
		}

		$maxsizeinput = $jemsettings->attachments_maxsize*1024; //size in kb

		foreach ($post_files['name'] as $k => $file) {

			if (empty($file)) {
				continue;
			}

			# check if the filetype is valid
			$fileext = strtolower(JFile::getExt($file));
			if (!in_array($fileext, $allowed)) {
				JError::raiseWarning(0, JText::_('COM_JEM_ERROR_ATTACHEMENT_EXTENSION_NOT_ALLOWED').': '.$file);
				continue;
			}
			# check size
			if ($post_files['size'][$k] > $maxsizeinput) {
				JError::raiseWarning(0, JText::sprintf('COM_JEM_ERROR_ATTACHEMENT_FILE_TOO_BIG', $file, $post_files['size'][$k], $maxsizeinput));
				continue;
			}

			if (!JFolder::exists($path)) {
				# try to create it
				$res = JFolder::create($path);
				if (!$res) {
					JError::raiseWarning(0, JText::_('COM_JEM_ERROR_COULD_NOT_CREATE_FOLDER').': '.$path);
					return false;
				}
				$file_content = '<!DOCTYPE html><title></title>';
				JFile::write($path.'/index.html', $file_content);
			}

			$sanitizedFilename = JemHelper::sanitize($path, $file);

			# Make sure that the full file path is safe.
			$filepath = JPath::clean( $path.'/'.$sanitizedFilename);
			JFile::upload($post_files['tmp_name'][$k], $filepath);

			$table = JTable::getInstance('Attachments', 'JEMTable');
			$table->file = $sanitizedFilename;
			$table->object = $object;
			if (isset($post_files['customname'][$k]) && !empty($post_files['customname'][$k])) {
				$table->name = $post_files['customname'][$k];
			}
			if (isset($post_files['description'][$k]) && !empty($post_files['description'][$k])) {
				$table->description = $post_files['description'][$k];
			}
			if (isset($post_files['access'][$k])) {
				$table->access = intval($post_files['access'][$k]);
			}
			$table->added = strftime('%F %T');
			$table->added_by = $user->get('id');

			if (!($table->check() && $table->store())) {
				JError::raiseWarning(0, JText::_('COM_JEM_ATTACHMENT_ERROR_SAVING_TO_DB').': '.$table->getError());
			}
		}

		return true;
	}

	/**
	 * update attachment record in db
	 * @param array (id, name, description, access)
	 */
	static function update($attach) {
		if (!is_array($attach) || !isset($attach['id']) || !(intval($attach['id']))) {
			return false;
		}
		$table = JTable::getInstance('Attachments', 'JEMTable');
		$table->load($attach['id']);
		$table->bind($attach);
		if (!($table->check() && $table->store())) {
			JError::raiseWarning(0, JText::_('COM_JEM_ATTACHMENT_ERROR_UPDATING_RECORD').': '.$table->getError());
			return false;
		}
		return true;
	}

	/**
	 * return attachments for objects
	 * @param string object identification (should be event<eventid>, category<categoryid>, etc...)
	 * @return array
	 */
	static function getAttachments($object) {
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$jemsettings	= JemHelper::config();
		$db				= JFactory::getDBO();
		$user			= JFactory::getUser();
		$levels			= $user->getAuthorisedViewLevels();
		$path			= JPATH_SITE.'/'.$jemsettings->attachments_path.'/'.$object;

		if (!file_exists($path)) {
			return array();
		}
		# first list files in the folder
		$files = JFolder::files($path, null, false, false);

		# then get info for files from db
		$fnames = array();
		foreach ($files as $f) {
			$fnames[] = $db->Quote($f);
		}
		if (!count($fnames)) {
			return array();
		}

		$query = $db->getQuery(true);
		$query->select(array('*'));
		$query->from('#__jem_attachments');
		$query->where(array('file IN ('.implode(',', $fnames) .')', 'object= '. $db->Quote($object),'access IN (' . implode(',', $levels) . ')'));

		$db->setQuery($query);
		$res = $db->loadObjectList();

		return $res;
	}

	/**
	 * get the file
	 */
	static function getAttachmentPath($id) {
		$jemsettings = JemHelper::config();

		$user = JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('*'));
		$query->from('#__jem_attachments');
		$query->where(array('id = '.$db->Quote(intval($id))));

		$db->setQuery($query);
		$res = $db->loadObject();

		if (!$res) {
			JError::raiseError(404, JText::_('COM_JEM_FILE_UNKNOWN'));
		}

		if (!in_array($res->access, $levels)) {
			JError::raiseError(403, JText::_('COM_JEM_YOU_DONT_HAVE_ACCESS_TO_THIS_FILE'));
		}

		$path = JPATH_SITE.'/'.$jemsettings->attachments_path.'/'.$res->object.'/'.$res->file;
		if (!file_exists($path)) {
			JError::raiseError(404, JText::_('COM_JEM_FILE_NOT_FOUND'));
		}

		return $path;
	}

	/**
	 * remove attachment for objects
	 *
	 * @param id from db
	 * @param string object identification (should be event<eventid>, category<categoryid>, etc...)
	 * @return boolean
	 */
	static function remove($id) {
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$jemsettings = JemHelper::config();

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('file','object'));
		$query->from('#__jem_attachments');
		$query->where(array('id = '.$db->Quote($id)));

		$db->setQuery($query);
		$res = $db->loadObject();
		if (!$res) {
			return false;
		}

		$path = JPATH_SITE.'/'.$jemsettings->attachments_path.'/'.$res->object.'/'.$res->file;
		if (file_exists($path)) {
			JFile::delete($path);
		}

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__jem_attachments'));
		$query->where('id = '.$db->Quote($id));
		$db->setQuery($query);
		$db->execute();

		$res = $db->execute();
		if (!$res) {
			return false;
		}

		return true;
	}
}