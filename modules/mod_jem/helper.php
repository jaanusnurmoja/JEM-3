<?php
/**
 * @version 3.0.5
 * @package JEM
 * @subpackage JEM Module
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_jem/models', 'JemModel');
require_once (JPATH_SITE.'/components/com_jem/helpers/helper.php');

# perform cleanup if it wasn't done today (archive, delete)
JEMHelper::cleanup();
/**
 * Module-Basic
 */
abstract class modJEMHelper
{

	/**
	 * Method to get the events
	 *
	 * @access public
	 * @return array
	 */
	public static function getList(&$params)
	{
		mb_internal_encoding('UTF-8');

		$db		= JFactory::getDBO();
		$user	= JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();

		# Retrieve Eventslist model for the data
		$model = JModelLegacy::getInstance('Eventslist', 'JemModel', array('ignore_request' => true));

		# Set params for the model
		# has to go before the getItems function
		$model->setState('params', $params);
		$model->setState('filter.access',true);

		# filter published
		#  0: unpublished
		#  1: published
		#  2: archived
		# -2: trashed

		$type = $params->get('type');

		# archived events
		if ($type == 2) {
			$model->setState('filter.published',2);
			$model->setState('filter.orderby',array('a.dates DESC','a.times DESC'));
			$cal_from = "";
		}

		# upcoming or running events, on mistake default to upcoming events
		else {
			$model->setState('filter.published',1);
			$model->setState('filter.orderby',array('a.dates ASC','a.times ASC'));

			$offset_minutes = 60 * $params->get('offset_hours', 0);

			$cal_from = "((TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(a.dates,' ',IFNULL(a.times,'00:00:00'))) > $offset_minutes) ";
			$cal_from .= ($type == 1) ? " OR (TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(IFNULL(a.enddates,a.dates),' ',IFNULL(a.endtimes,'23:59:59'))) > $offset_minutes)) " : ") ";
		}

		$model->setState('filter.calendar_from',$cal_from);
		$model->setState('filter.groupby','a.id');

		# clean parameter data
		$catids = $params->get('catid');
		$venids = $params->get('venid');
		$eventids = $params->get('eventid');
		
		# filter category's
		if ($catids) {
			$model->setState('filter.category_id',$catids);
			$model->setState('filter.category_id.include',true);
		}

		# filter venue's
		if ($venids) {
			$model->setState('filter.venue_id',$venids);
			$model->setState('filter.venue_id.include',true);
		}

		# filter event id's
		if ($eventids) {
			$model->setState('filter.event_id',$eventids);
			$model->setState('filter.event_id.include',true);
		}
		
		# count
		$count = $params->get('count', '2');

		$model->setState('list.limit',$count);

		# Retrieve the available Events
		$events = $model->getItems();

		# Loop through the result rows and prepare data
		$i		= 0;
		$lists	= array();

		foreach ($events as $row)
		{
			//cut titel
			$length = mb_strlen($row->title);

			if ($length > $params->get('cuttitle', '18')) {
				$row->title = mb_substr($row->title, 0, $params->get('cuttitle', '18'));
				$row->title = $row->title.'...';
			}

			$lists[$i] = new stdClass;
			$lists[$i]->link		= JRoute::_(JEMHelperRoute::getEventRoute($row->slug));
			$lists[$i]->dateinfo 	= JEMOutput::formatShortDateTime($row->dates, $row->times,
						$row->enddates, $row->endtimes);
			$lists[$i]->text		= $params->get('showtitloc', 0) ? $row->title : htmlspecialchars($row->venue, ENT_COMPAT, 'UTF-8');
			$lists[$i]->venue		= htmlspecialchars($row->venue, ENT_COMPAT, 'UTF-8');
			$lists[$i]->city		= htmlspecialchars($row->city, ENT_COMPAT, 'UTF-8');
			$lists[$i]->venueurl 	= !empty($row->venueslug) ? JRoute::_(JEMHelperRoute::getVenueRoute($row->venueslug)) : null;
			$i++;
		}

		return $lists;
	}


	/**
	 * Method to get a valid url
	 *
	 * @access public
	 * @return string
	 */
	protected static function _format_url($url)
	{
		if(!empty($url) && strtolower(substr($url, 0, 7)) != "http://") {
			$url = 'http://'.$url;
		}
		return $url;
	}
}