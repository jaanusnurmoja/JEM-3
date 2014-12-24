<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jem')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JHtml::_('behavior.tabstate');

// Require classes
require_once (JPATH_COMPONENT_SITE.'/helpers/helper.php');
require_once (JPATH_COMPONENT_SITE.'/helpers/countries.php');
require_once (JPATH_COMPONENT_SITE.'/classes/image.class.php');
require_once (JPATH_COMPONENT_SITE.'/classes/output.class.php');
require_once (JPATH_COMPONENT_SITE.'/classes/user.class.php');
require_once (JPATH_COMPONENT_SITE.'/classes/attachment.class.php');
require_once (JPATH_COMPONENT_SITE.'/classes/categories.class.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/admin.class.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

# load recurrence files
JLoader::registerNamespace('Recurr', JPATH_COMPONENT_SITE . '/classes');

# Set the table directory
JTable::addIncludePath(JPATH_COMPONENT.'/tables');

# perform cleanup if it wasn't done today (archive, delete, recurrence)
JemHelper::cleanup();

# Get an instance of the controller
$controller = JControllerLegacy::getInstance('Jem');

# Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

# Redirect if set by the controller
$controller->redirect();
?>