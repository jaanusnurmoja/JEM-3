<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Class-JemView
 */
class JEMView extends JViewLegacy {
	/**
	 * Adds a row to data indicating even/odd row number
	 *
	 * @return object $rows
	 */
	public function getRows($rowname = "rows")
	{
		if (!isset($this->$rowname) || !count($this->$rowname)) {
			return;
		}

		$k = 0;
		foreach($this->$rowname as $row) {
			$row->odd = $k;
			$k = 1 - $k;
		}

		return $this->$rowname;
	}

	/**
	 * Add path for common templates.
	 */
	protected function addCommonTemplatePathDISABLED() {
		// additional path for list part + corresponding override path
		$this->addTemplatePath(JPATH_COMPONENT.'/common/views/tmpl');
		$this->addTemplatePath(JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/com_jem/common');
	}

	/**
	 * Prepares the document.
	 */
	protected function prepareDocumentDISABLED() {
		$app 		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$menu 		= $menus->getActive();
		$print		= JRequest::getBool('print');

		if ($print) {
			JemHelper::loadCss('print');
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			// TODO
			$this->params->def('page_heading', JText::_('COM_JEM_DEFAULT_PAGE_TITLE_DAY'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = $app->getCfg('sitename');
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		// TODO: Metadata
		$this->document->setMetadata('keywords', $this->params->get('page_title'));
	}
}