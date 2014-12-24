<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;


/**
 * Venue Select
 */
class JFormFieldModal_Venue extends JFormField
{
	/**
	 * field type
	 * @var string
	 */
	protected $type = 'Modal_Venue';


	/**
	 * Method to get the field input markup
	 */
	protected function getInput()
	{
		$allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;
		
		// Load modal behavior
		JHtml::_('behavior.modal', 'a.flyermodal');

		// Build the script
		$script = array();
		$script[] = '    function jSelectVenue_'.$this->id.'(id, venue, object) {';
		$script[] = '        document.id("'.$this->id.'_id").value = id;';
		$script[] = '        document.id("'.$this->id.'_name").value = venue;';
		$script[] = '		jQuery("#'.$this->id.'_clear").removeClass("hidden");';
		$script[] = '        SqueezeBox.close();';
		$script[] = '    }';

		
		// Clear button script
		static $scriptClear;
		
		if ($allowClear && !$scriptClear)
		{
			$scriptClear = true;
		
			$script[] = '	function jClear(id) {';
			$script[] = '		document.getElementById(id + "_id").value = "";';
			$script[] = '		document.getElementById(id + "_name").value = "'.htmlspecialchars(JText::_('COM_JEM_SELECT_VENUE', true), ENT_COMPAT, 'UTF-8').'";';
			$script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
			$script[] = '		if (document.getElementById(id + "_edit")) {';
			$script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
			$script[] = '		}';
			$script[] = '		return false;';
			$script[] = '	}';
		}
		
		
		// Add to document head
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display
		$html = array();
		$link = 'index.php?option=com_jem&amp;view=venueelement&amp;tmpl=component&amp;function=jSelectVenue_'.$this->id;

		if ((int) $this->value > 0)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('venue');
			$query->from('#__jem_venues');
			$query->where(array('id='.(int)$this->value));
			$db->setQuery($query);

			try
			{
				$venue = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JError::raiseWarning(500, $e->getMessage());
			}		
		}
				
		if (empty($venue)) {
			$venue = JText::_('COM_JEM_SELECT_VENUE');
		}
		$venue = htmlspecialchars($venue, ENT_QUOTES, 'UTF-8');

		
		// The active venue id field
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}
		
		// The current venue input field
		$html[] = '<span class="input-append">';
		$html[] = '  <input type="text" class="input-medium" id="'.$this->id.'_name" value="'.$venue.'" disabled="disabled" size="35" />';
		$html[] = '<a class="flyermodal btn" href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JSELECT').'</a>';
		$html[] = '<button id="'.$this->id.'_clear" class="btn'.($value ? '' : ' hidden').'" onclick="return jClear(\''.$this->id.'\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</button>';
		$html[] = '</span>';
		
		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}
?>