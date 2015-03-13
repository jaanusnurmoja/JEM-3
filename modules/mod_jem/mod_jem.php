<?php
/**
 * @package JEM
 * @subpackage JEM - Module-Basic
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

// get helper
require_once __DIR__ . '/helper.php';
require_once JPATH_SITE . '/components/com_jem/helpers/route.php';
require_once JPATH_SITE . '/components/com_jem/helpers/helper.php';
require_once JPATH_SITE . '/components/com_jem/classes/output.class.php';

$list = modJEMHelper::getList($params);

// check if any results returned
$items = count($list);
if (!$items) {
    return;
}

require JModuleHelper::getLayoutPath('mod_jem');
