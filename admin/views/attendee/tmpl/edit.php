<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal', 'a.flyermodal');

?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'attendee.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_jem&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<fieldset class="form-horizontal"><legend><?php echo JText::_('COM_JEM_DETAILS'); ?></legend>
	
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('uid');?></div>
			<div class="controls"><?php echo $this->form->getInput('uid'); ?></div>
		</div>
	</fieldset>

<?php
echo JHtml::_('form.token');
?>
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="eid" value="<?php echo $this->eventid; ?>" />
<input type="hidden" name="task" value="" />
</form>


<?php
//keep session alive while editing
JHtml::_('behavior.keepalive');
?>