<?php
/**
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

/**
 * Model: Source
 */
class JEMModelSource extends JModelForm
{
	/**
	 * Cache for the template information.
	 *
	 * @var		object
	 */
	private $_template = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState()
	{
		jimport('joomla.filesystem.file');

		$app = JFactory::getApplication('administrator');

		// Load the User state.
		$id = $app->getUserState('com_jem.edit.source.id');

		// Parse the template id out of the compound reference.
		$temp = (base64_decode($id));
		$fileName = $temp;

		$this->setState('filename', $fileName);

		// Save the syntax for later use
		$app->setUserState('editor.source.syntax', JFile::getExt($fileName));

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_jem');
		$this->setState('params', $params);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Codemirror or Editor None should be enabled
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__extensions as a');
		$query->where('(a.name ='.$db->quote('plg_editors_codemirror').' AND a.enabled = 1) OR (a.name ='.$db->quote('plg_editors_none').' AND a.enabled = 1)');
		$db->setQuery($query);
		$state = $db->loadResult();
		if ((int)$state < 1 ) {
			$app->enqueueMessage(JText::_('COM_JEM_CSSMANAGER_ERROR_EDITOR_DISABLED'), 'warning');
		}

		// Get the form.
		$form = $this->loadForm('com_jem.source', 'source', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jem.edit.source.data', array());

		if (empty($data)) {
			$data = $this->getSource();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getSource()
	{
		$item = new stdClass;
		$item->custom = false;

		$fileName	= $this->getState('filename');
		$custom		= stripos($fileName, 'custom#:');

		$filePath	= JPath::clean(JPATH_ROOT.'/media/com_jem/css/'.$fileName);

		# custom file?
		if ($custom !== false) {
			$file = str_replace('custom#:', '', $fileName);
			$filePath	= JPath::clean(JPATH_SITE.'/'.$file);
		}

		if (file_exists($filePath)) {
			jimport('joomla.filesystem.file');
			$item->filename = $this->getState('filename');
			if ($custom !== false) {
				$item->filename = $file;
				$item->custom	= true;
			}
			$item->source = file_get_contents($filePath);
		}

		if (empty($item->source)) {
			$this->setError(JText::_('COM_JEM_CSSMANAGER_ERROR_SOURCE_FILE_NOT_FOUND'));
		}

		return $item;
	}

	/**
	 * Method to store the source file contents.
	 *
	 * @param	array	The souce data to save.
	 *
	 * @return	boolean	True on success, false otherwise and internal error set.
	 */
	public function save($data)
	{
		jimport('joomla.filesystem.file');

		$dispatcher = JEventDispatcher::getInstance();
		$fileName	= $this->getState('filename');
		$custom		= stripos($fileName, 'custom#:');

		$filePath	= JPath::clean(JPATH_ROOT.'/media/com_jem/css/'.$fileName);

		# custom file?
		if ($custom !== false) {
			$file = str_replace('custom#:', '', $fileName);
			$filePath	= JPath::clean(JPATH_SITE.'/'.$file);
		}

		// Include the extension plugins for the save events.
		JPluginHelper::importPlugin('extension');

		// Set FTP credentials, if given.
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');

		// Try to make the template file writeable.
		if (!$ftp['enabled'] && JPath::isOwner($filePath) && !JPath::setPermissions($filePath, '0644')) {
			$this->setError(JText::_('COM_JEM_CSSMANAGER_ERROR_SOURCE_FILE_NOT_WRITABLE'));
			return false;
		}

		// Trigger the onExtensionBeforeSave event.
		$result = $dispatcher->trigger('onExtensionBeforeSave', array('com_jem.source', &$data, false));
		if (in_array(false, $result, true)) {
			$this->setError($table->getError());
			return false;
		}

		$return = JFile::write($filePath, $data['source']);

		// Try to make the template file unwriteable.
		if (!$ftp['enabled'] && JPath::isOwner($filePath) && !JPath::setPermissions($filePath, '0444')) {
			$this->setError(JText::_('COM_JEM_CSSMANAGER_ERROR_SOURCE_FILE_NOT_UNWRITABLE'));
			return false;
		} elseif (!$return) {
			$this->setError(JText::sprintf('COM_JEM_CSSMANAGER_ERROR_FAILED_TO_SAVE_FILENAME', $fileName));
			return false;
		}

		// Trigger the onExtensionAfterSave event.
		$dispatcher->trigger('onExtensionAfterSave', array('com_jem.source', &$table, false));

		return true;
	}
}
