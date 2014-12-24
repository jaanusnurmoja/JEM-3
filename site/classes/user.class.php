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
 * Holds all authentication logic
 */
class JEMUser {

	/**
	 * Checks access permissions of the user regarding on the groupid
	 *
	 * @param int $recurse
	 * @param int $level
	 * @return boolean True on success
	 */
	static function validate_user($recurse, $level) {
		$user = JFactory::getUser();

		// Only check when user is logged in
		if ( $user->get('id') ) {
			//open for superuser or registered and thats all what is needed
			//level = -1 all registered users
			//level = -2 disabled
			if ((( $level == -1 ) && ( $user->get('id') )) || (( JFactory::getUser()->authorise('core.manage') ) && ( $level == -2 ))) {
				return true;
			}
		}
		// User has no permissions
		return false;
	}

	/**
	 * Checks if the user is allowed to edit an item
	 *
	 *
	 * @param int $allowowner
	 * @param int $ownerid
	 * @param int $recurse
	 * @param int $level
	 * @return boolean True on success
	 */
	static function editaccess($allowowner, $ownerid, $recurse, $level) {
		$user = JFactory::getUser();

		$generalaccess = JEMUser::validate_user( $recurse, $level );

		if ($allowowner == 1 && ( $user->get('id') == $ownerid && $ownerid != 0 ) ) {
			return true;
		} elseif ($generalaccess == 1) {
			return true;
		}
		return false;
	}

	/**
	 * Checks if the user is a superuser
	 * A superuser will allways have access if the feature is activated
	 *
	 * @return boolean True on success
	 */
	static function superuser() {

		$user = JFactory::getUser();

    	if($user->authorise('core.manage', 'com_jem')) {
    		return true;
    	} else {
    		return false;
    	}

	}
	
	/**
	 * Checks if the user has the privileges to use the wysiwyg editor
	 *
	 * We could use the validate_user method instead of this to allow to set a groupid
	 * Not sure if this is a good idea
	 *
	 * @return boolean True on success
	 */
	static function editoruser() {
		$user 		= JFactory::getUser();
		$userGroups = $user->getAuthorisedGroups();

		$group_ids = array(
 					2, // registered
					3, // author
					4, // editor
					5, // publisher
					6, // manager
					7, // administrator
					8  // Super Users
					);

		foreach ($userGroups as $gid) {
			if (in_array($gid, $group_ids)) return true;
		}

		return false;
	}

	/**
	 * Checks if the user is a maintainer of a category
	 *
	 * @return NULL int of maintained categories or null
	 */
	static function ismaintainer($action, $eventid = false)
	{

		// lets look if the user is a maintainer
		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		$query = 'SELECT gr.id' . ' FROM #__jem_groups AS gr'
				. ' LEFT JOIN #__jem_groupmembers AS g ON g.group_id = gr.id'
				. ' WHERE g.member = ' . (int) $user->get('id')
				. ' AND ' .$db->quoteName('gr.' . $action . 'event') . ' = 1 '
				. ' AND g.member NOT LIKE 0';
		$db->setQuery($query);
		$groupnumber = $db->loadColumn();

		// no results, the user is not within a group with the required right
		if (!$groupnumber) {
			return false;
		}

		// the user is in a group with the required right but is there a
		// published category where he is allowed to post in?

		$categories = implode(' OR groupid = ', $groupnumber);

		if ($action == 'edit') {
			$query = 'SELECT a.catid' . ' FROM #__jem_cats_event_relations AS a'
					. ' LEFT JOIN #__jem_categories AS c ON c.id = a.catid'
					. ' WHERE c.published = 1'
					. ' AND (c.groupid = ' . $categories . ')'
					. ' AND a.itemid = ' . $eventid;
			$db->setQuery($query);
		}
		else {
			$query = 'SELECT id' . ' FROM #__jem_categories'
					. ' WHERE published = 1'
					. ' AND (groupid = ' . $categories . ')';
			$db->setQuery($query);
		}

		$maintainer = $db->loadResult();

		if (!$maintainer) {
			return false;
		}
		else {
			return true;
		}
	}


	/**
	 * Checks if an user is a groupmember and if so
	 * if the group is allowed to add-venues
	 *
	 */
	static function venuegroups($action) {
		//lets look if the user is a maintainer
		$db 	= JFactory::getDBO();
		$user	= JFactory::getUser();

		/*
		 * just a basic check to see if the current user is in an usergroup with
		 * access for submitting venues. if a result then return true, otherwise false
		 *
		 * Actions: addvenue, publishvenue, editvenue
		 *
		 * views: venues, venue, editvenue
		 */
		$query = 'SELECT gr.id'
				. ' FROM #__jem_groups AS gr'
				. ' LEFT JOIN #__jem_groupmembers AS g ON g.group_id = gr.id'
				. ' AND '.$db->quoteName('gr.'.$action.'venue').' = 1 '
				. ' WHERE g.member = '.(int) $user->get('id')
				. ' AND g.member NOT LIKE 0';
				;
		$db->setQuery($query);

		$groupnumber = $db->loadResult();

		//no results
		if (!$groupnumber) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * validates guest rights
	 */
	static function validate_guest($option = false) {
		
		$user 		= JFactory::getUser();
		$guest 		= $user->get('guest');
		$settings 	= JemHelper::globalattribs();
		
		
		if ($guest) {
		
			# check if he global setting has been set
			$addevent	= $settings->get('guest_addevent',0);
		
			if (!$addevent) {
				return false;
			}
		
			# then check if we have 1 of the antispam measures enabled
			# if not then the guest is not allowed to submit events
		
			$mathquiz	= $settings->get('guest_as_math',0);
			$captcha	= $settings->get('guest_as_captcha',0);
		
			if (!$mathquiz && !$captcha) {
				return false;
			}
			
			return true;
		
		}
	}
	
}