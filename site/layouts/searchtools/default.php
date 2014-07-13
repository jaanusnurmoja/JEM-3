<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

// Set some basic options
$customOptions = array(
	'filtersHidden'       => isset($data['options']['filtersHidden']) ? $data['options']['filtersHidden'] : empty($data['view']->activeFilters),
	'defaultLimit'        => isset($data['options']['defaultLimit']) ? $data['options']['defaultLimit'] : JFactory::getApplication()->getCfg('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#list_fullordering'
);

$data['options'] = array_unique(array_merge($customOptions, $data['options']));

$formSelector = !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm';

// Load search tools
JHtml::_('searchtools.form', $formSelector, $data['options']);
$filterButton = !empty($data['view']->filterButton) ? $data['view']->filterButton : false;

?>
<div class="js-stools clearfix">
	<div class="clearfix">
		<div class="js-stools-container-bar">
			<?php //echo JLayoutHelper::render('joomla.searchtools.default.bar', $data); ?>
			<?php echo JLayoutHelper::render('searchtools.default.bar', $data,JPATH_ROOT .'/components/com_jem/layouts');?>
		</div>
		<div class="js-stools-container-list hidden-phone hidden-tablet">
			<?php //echo JLayoutHelper::render('joomla.searchtools.default.list', $data); ?>
			<?php echo JLayoutHelper::render('searchtools.default.list', $data,JPATH_ROOT .'/components/com_jem/layouts');?>
		</div>
	</div>
	<!-- Filters div -->
	<?php if ($filterButton) { ?>
	
	<div class="js-stools-container-filters hidden-phone clearfix">
		<?php  echo JLayoutHelper::render('joomla.searchtools.default.filters', $data); ?>
		<?php  echo JLayoutHelper::render('searchtools.default.filters', $data,JPATH_ROOT .'/components/com_jem/layouts');?>
	</div>
	<?php } ?>
</div>