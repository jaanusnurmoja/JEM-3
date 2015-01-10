<?php
/**
 * @version 3.0.6
 * @package JEM
 * @copyright (C) 2013-2015 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * View: Venueelement
 */
class JemViewVenueelement extends JViewLegacy {

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;

		// initialise variables
		$db			= JFactory::getDBO();
		$document	= JFactory::getDocument();
		$itemid 	= $jinput->getInt('id', 0) . ':' . $jinput->getInt('Itemid', 0);

		// get vars
		$filter_order		= $app->getUserStateFromRequest('com_jem.venueelement.'.$itemid.'.filter_order', 'filter_order', 'l.ordering', 'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest('com_jem.venueelement.'.$itemid.'.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_type 		= $app->getUserStateFromRequest('com_jem.venueelement.'.$itemid.'.filter_type', 'filter_type', '', 'int');
		$search 			= $app->getUserStateFromRequest('com_jem.venueelement.'.$itemid.'.filter_search', 'filter_search', '', 'string');
		$search 			= $db->escape(trim(JString::strtolower($search)));

		// prepare document
		$document->setTitle(JText::_('COM_JEM_SELECTVENUE'));
		
		// Load css
		JHtml::_('stylesheet', 'com_jem/backend.css', array(), true);

		// Get data from the model
		$rows = $this->get('Data');

		// add pagination
		$pagination = $this->get('Pagination');

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// Build search filter
		$filters = array();
		$filters[] = JHtml::_('select.option', '1', JText::_('COM_JEM_VENUE'));
		$filters[] = JHtml::_('select.option', '2', JText::_('COM_JEM_CITY'));
		$filters[] = JHtml::_('select.option', '3', JText::_('COM_JEM_STATE'));
		$lists['filter'] = JHtml::_('select.genericlist', $filters, 'filter_type', array('size'=>'1','class'=>'inputbox'), 'value', 'text', $filter_type);

		// search filter
		$lists['search']= $search;

		// assign data to template
		$this->lists		= $lists;
		$this->rows			= $rows;
		$this->pagination	= $pagination;

		parent::display($tpl);
	}
}
?>