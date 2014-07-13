<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
?>
<div class="span6">
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_GENERAL_LAYOUT_SETTINGS'); ?></legend>
			<?php foreach ($this->form->getFieldset('layoutgenerallayoutsetting') as $field): ?>
			<div class="control-group">	
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	</fieldset>
	
	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_CITY_COLUMN'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('showcity'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('showcity'); ?></div>
		</div>
		<div id="city1" style="display:none" class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('citywidth'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('citywidth'); ?></div>
		</div>
	</fieldset>

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_ATTENDEE_COLUMN'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('showatte'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('showatte'); ?></div>
		</div>

		<div id="atte1" style="display:none" class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('attewidth'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('attewidth'); ?></div>
		</div>
	</fieldset>

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_TITLE_COLUMN'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('showtitle'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('showtitle'); ?></div>
		</div>
			
		<div id="title1" style="display:none" class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('titlewidth'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('titlewidth'); ?></div>
		</div>
	</fieldset>

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_VENUE_COLUMN'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('showlocate'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('showlocate'); ?></div>
		</div>

		<div id="loc1" style="display:none" class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('locationwidth'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('locationwidth'); ?></div>
		</div>

		<div id="loc2" style="display:none" class="control-group">	
			<div class="control-label"><?php echo $this->form->getLabel('showlinkvenue'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('showlinkvenue'); ?></div>
		</div>
	</fieldset>

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_CATEGORY_COLUMN'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('showcat'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('showcat'); ?></div>
		</div>
		<div id="cat1" style="display:none" class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('catfrowidth'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('catfrowidth'); ?></div>
		</div>
		<div id="cat2" style="display:none" class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('catlinklist'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('catlinklist'); ?></div>
		</div>
	</fieldset>

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_LAYOUT_TABLE_EVENTIMAGE'); ?></legend>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('showeventimage'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('showeventimage'); ?></div>
		</div>
		<div id="evimage1" style="display:none" class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('tableeventimagewidth'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('tableeventimagewidth'); ?></div>
		</div>
	</fieldset>

</div><div class="span6">

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_CSS'); ?></legend>
			<?php foreach ($this->form->getFieldset('stylesheet') as $field): ?>
			<div class="control-group">	
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	</fieldset>

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_CSS_COLOR_BACKGROUND'); ?></legend>
			<?php foreach ($this->form->getFieldset('css_color') as $field): ?>
			<div class="control-group">	
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	</fieldset>

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_CSS_COLOR_BORDER'); ?></legend>
			<?php foreach ($this->form->getFieldset('css_color_border') as $field): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	</fieldset>

	<fieldset class="form-horizontal">
		<legend><?php echo JText::_('COM_JEM_SETTINGS_LEGEND_CSS_COLOR_FONT'); ?></legend>
			<?php foreach ($this->form->getFieldset('css_color_font') as $field): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
			<?php endforeach; ?>
	</fieldset>
</div>