<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Controller: Category
 */
class JemControllerCategory extends JControllerForm
{
	/**
	 * The extension for which the categories apply.
	 *
	 * @var    string
	 */
	protected $text_prefix = 'COM_JEM_CATEGORY';

	/**
	 * Constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 *
	 * @see    JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

}