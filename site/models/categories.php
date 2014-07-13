<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * JEM Component Categories Model
 *
 * @package JEM
 *
 */
class JEMModelCategories extends JModelLegacy
{
	/**
	 * Top category id
	 *
	 * @var int
	 */
	var $_id = 0;

	/**
	 * Event data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Categories total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Categories data array
	 *
	 * @var array
	 */
	var $_categories = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Show empty categories in list
	 *
	 * @var bool
	 */
	protected $_showemptycats    = false;

	/**
	 * Show subcategories
	 *
	 * @var bool
	 */
	protected $_showsubcats      = false;

	/**
	 * Show empty subcategories
	 *
	 * @var bool
	 */
	protected $_showemptysubcats = false;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$app = JFactory::getApplication();

		// Get the parameters of the active menu item
		$params = $app->getParams('com_jem');

		if (JRequest::getInt('id')) {
			$id = JRequest::getInt('id');
		} else {
			$id = $params->get('id', 1);
		}

		$this->_id = $id;

		$this->_showemptycats    = (bool)$params->get('showemptycats', 1);
		$this->_showsubcats      = (bool)$params->get('usecat', 1);
		$this->_showemptysubcats = (bool)$params->get('showemptychilds', 1);

		//get the number of events from database
		$limit 		= JRequest::getInt('limit', $params->get('cat_num'));
		$limitstart = JRequest::getInt('limitstart');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the Categories
	 *
	 * @access public
	 * @return array
	 */
	function &getData()
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Lets load the content if it doesn't already exist
		if (empty($this->_categories))
		{
			// include category itself but not if it's the root category
			$parentCategory = ($this->_id > 1) ? $this->_getList($this->_buildQueryParentCategory(true)) : array();
			$query = $this->_buildQuerySubCategories($this->_showemptycats);
			$pagination = $this->getPagination();
			$this->_categories = $this->_getList($query, $pagination->limitstart, $pagination->limit);

			// Include parent category itself
			$this->_categories = array_merge($parentCategory, $this->_categories);

			foreach($this->_categories as $category) {
				if ($this->_showsubcats) {
					//child categories
					// ensure parent shows at least all categories also shown in list
					$showempty = $this->_showemptysubcats | ($category->id == $this->_id ? $this->_showemptycats : false);
					$query = $this->_buildQuerySubCategories($showempty, $category->id);
					$this->_db->setQuery($query);
					$category->subcats = $this->_db->loadObjectList();
				} else {
					$category->subcats = array();
				}

				//Generate description
				if (empty ($category->description)) {
					$category->description = JText::_('COM_JEM_NO_DESCRIPTION');
				} else {
					//execute plugins
					$category->text 	= $category->description;
					$category->title 	= $category->catname;
					JPluginHelper::importPlugin('content');
					$app->triggerEvent('onContentPrepare', array('com_jem.categories', &$category, &$params, 0));
					$category->description = $category->text;
				}

				//create target link
				// TODO: Move to view?
				$task = JRequest::getWord('task');
				if ($task == 'archive') {
					$category->linktext   = JText::_('COM_JEM_SHOW_ARCHIVE');
					$category->linktarget = JRoute::_(JEMHelperRoute::getCategoryRoute($category->slug.'&task=archive'));
				} else {
					$category->linktext   = JText::_('COM_JEM_SHOW_EVENTS');
					$category->linktarget = JRoute::_(JEMHelperRoute::getCategoryRoute($category->slug));
				}
			}
		}

		return $this->_categories;
	}

	/**
	 * Total nr of Categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the total nr if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQueryTotal();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get the Categories events
	 *
	 * @access public
	 * @return array
	 */
	function & getEventdata($id)
	{
		$app = JFactory::getApplication();

		$params = $app->getParams('com_jem');

		// Lets load the content
		$query = $this->_buildDataQuery($id);
		$this->_data = $this->_getList($query, 0, $params->get('detcat_nr'));

		$count = count($this->_data);
		for ($i = 0; $i < $count; $i++) {
			$item = $this->_data[$i];
			$item->categories = $this->getCategories($item->id);

			//remove events without categories (users have no access to them)
			if (empty($item->categories)) {
				unset ($this->_data[$i]);
			}
		}

		return $this->_data;
	}

	/**
	 * Method get the event query
	 *
	 * @access private
	 * @return array
	 */
	protected function _buildDataQuery($id)
	{
		$user = JFactory::getUser();
		// Support Joomla access levels instead of single group id
		$levels = $user->getAuthorisedViewLevels();

		$id = (int)$id;

		$task = JRequest::getWord('task');

		// First thing we need to do is to select only the requested events
		if ($task == 'archive') {
			$where = ' WHERE a.published = 2 && rel.catid = '.$id;
		} else {
			$where = ' WHERE a.published = 1 && rel.catid = '.$id;
		}

		// Second is to only select events assigned to category the user has access to
		$where .= ' AND c.access IN (' . implode(',', $levels) . ')';

		$query = 'SELECT DISTINCT a.id, a.dates, a.enddates, a.times, a.endtimes, a.title, a.locid, a.created, l.venue, l.city, l.state, l.url,'
			.' a.recurrence_type, a.recurrence_first_id,'
			.' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'
			.' CASE WHEN CHAR_LENGTH(l.alias) THEN CONCAT_WS(\':\', a.locid, l.alias) ELSE a.locid END as venueslug'
			.' FROM #__jem_events AS a'
			.' LEFT JOIN #__jem_venues AS l ON l.id = a.locid'
			.' LEFT JOIN #__jem_cats_event_relations AS rel ON rel.itemid = a.id'
			.' LEFT JOIN #__jem_categories AS c ON c.id = '.$id
			.$where
			.' ORDER BY a.dates, a.times'
		;

		return $query;
	}

	function getCategories($id)
	{
		$user = JFactory::getUser();
		// Support Joomla access levels instead of single group id
		$levels = $user->getAuthorisedViewLevels();

		$query = 'SELECT DISTINCT c.id, c.catname, c.access, c.checked_out AS cchecked_out,'
			.' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug'
			.' FROM #__jem_categories AS c'
			.' LEFT JOIN #__jem_cats_event_relations AS rel ON rel.catid = c.id'
			.' WHERE rel.itemid = '.(int)$id
			.' AND c.published = 1'
			.' AND c.access IN (' . implode(',', $levels) . ')'
		;

		$this->_db->setQuery($query);

		$this->_cats = $this->_db->loadObjectList();
		return $this->_cats;
	}

	/**
	 * Method to get the subcategories query
	 * @param bool   $emptycat include empty categories
	 * @param string $parent_id Parent ID of the subcategories
	 * @return string The query string
	 */
	protected function _buildQuerySubCategories($emptycat, $parent_id = null) {
		return $this->_buildQuery($emptycat, $parent_id);
	}

	/**
	 * Method to get the parent category query
	 * @param bool   $emptycat include empty categories
	 * @param string $parent_id ID of the parent category
	 * @return string The query string
	 */
	protected function _buildQueryParentCategory($emptycat, $parent_id = null) {
		return $this->_buildQuery($emptycat, $parent_id, true);
	}


	/**
	 * Method to get the categories query
	 * @param bool   $emptycat include empty categories
	 * @param string $parent_id
	 * @param bool   $parentCategory
	 * @return string The query string
	 */
	protected function _buildQuery($emptycat, $parent_id = null, $parentCategory = false)
	{
		if (is_null($parent_id)) {
			$parent_id = $this->_id;
		}

		$user = JFactory::getUser();
		// Support Joomla access levels instead of single group id
		$levels = $user->getAuthorisedViewLevels();

		$ordering = 'c.ordering ASC';

		// build where clause
		$where_sub = ' WHERE cc.published = 1';
		if($parentCategory) {
			$where_sub .= ' AND cc.id = '.(int) $parent_id;
		} else {
			$where_sub .= ' AND cc.parent_id = '.(int) $parent_id;
		}
		$where_sub .= ' AND cc.access IN (' . implode(',', $levels) . ')';

		// check archive task and ensure that only categories get selected
		// if they contain a published/archived event
		$task = JRequest::getWord('task');
		if($task == 'archive') {
			$where_sub .= ' AND i.published = 2';
		} else {
			$where_sub .= ' AND i.published = 1';
		}
		$where_sub .= ' AND c.id = cc.id';

		// show/hide empty categories
		$empty = $emptycat ? '' : ' HAVING assignedevents > 0';

		// Parent category itself or its sub categories
		$parentCategoryQuery = $parentCategory ? 'c.id='.(int)$parent_id : 'c.parent_id='.(int)$parent_id;

		$query = 'SELECT c.*,'
				. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS slug,'
					. ' ('
					. ' SELECT COUNT(DISTINCT i.id)'
					. ' FROM #__jem_events AS i'
					. ' LEFT JOIN #__jem_cats_event_relations AS rel ON rel.itemid = i.id'
					. ' LEFT JOIN #__jem_categories AS cc ON cc.id = rel.catid'
					. $where_sub
					. ' GROUP BY cc.id'
					. ')'
					. ' AS assignedevents'
				. ' FROM #__jem_categories AS c'
				. ' WHERE c.published = 1'
				. ' AND '.$parentCategoryQuery
				. ' AND c.access IN (' . implode(',', $levels) . ')'
				. ' GROUP BY c.id '.$empty
				. ' ORDER BY '.$ordering
				;

		return $query;
	}


	/**
	 * Method to build the Categories query without subselect
	 * That's enough to get the total value.
	 *
	 * @access private
	 * @return string
	 */
	protected function _buildQueryTotal()
	{
		$user = JFactory::getUser();
		// Support Joomla access levels instead of single group id
		$levels = $user->getAuthorisedViewLevels();

		$query = 'SELECT DISTINCT c.id'
			. ' FROM #__jem_categories AS c';

		if (!$this->_showemptycats) {
			$query .= ' INNER JOIN #__jem_cats_event_relations AS rel ON rel.catid = c.id '
					. ' INNER JOIN #__jem_events AS e ON e.id = rel.itemid ';
		}

		$query .= ' WHERE c.published = 1'
			. ' AND c.parent_id = ' . (int) $this->_id
			. ' AND c.access IN (' . implode(',', $levels) . ')'
			;

		if (!$this->_showemptycats) {
			$task = JRequest::getWord('task');
			if($task == 'archive') {
				$query .= ' AND e.published = 2';
			} else {
				$query .= ' AND e.published = 1';
			}
		}

		return $query;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_pagination;
	}
}
?>