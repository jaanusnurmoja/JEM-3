<?php
/**
 * @package JEM
 * @subpackage JEM - Module-Wide
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

// get module helper
require_once __DIR__ . '/helper.php';

// require needed component classes
require_once JPATH_SITE . '/components/com_jem/helpers/helper.php';
require_once JPATH_SITE . '/components/com_jem/helpers/route.php';
require_once JPATH_SITE . '/components/com_jem/classes/image.class.php';
require_once JPATH_SITE . '/components/com_jem/classes/Zebra_Image.php';
require_once JPATH_SITE . '/components/com_jem/classes/output.class.php';

$list = modJEMwideHelper::getList($params);

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base(true).'/modules/mod_jem_wide/tmpl/mod_jem_wide.css');

// check if any results returned
$items = count($list);
if (!$items) {
	return;
}
require JModuleHelper::getLayoutPath('mod_jem_wide');
