<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * View: Source
 */
class JEMViewSource extends JViewLegacy
{
	protected $form;
	protected $source;
	protected $state;
	protected $template;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->form		= $this->get('Form');
		$this->source	= $this->get('Source');
		$this->state	= $this->get('State');
		$this->template	= $this->get('Template');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		$jinput = JFactory::getApplication()->input;
		$jinput->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$canDo		= JEMHelperBackend::getActions(0);

		JToolBarHelper::title(JText::_('COM_JEM_CSSMANAGER_EDIT_FILE'), 'thememanager');

		// Can save the item.
		if ($canDo->get('core.edit')) {
			JToolBarHelper::apply('source.apply');
			JToolBarHelper::save('source.save');
		}

		JToolBarHelper::cancel('source.cancel', 'JTOOLBAR_CLOSE');
		JToolBarHelper::divider();
		JToolBarHelper::help('editcss', true);
	}
}