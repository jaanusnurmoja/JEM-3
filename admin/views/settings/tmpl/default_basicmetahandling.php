<?php
/**
 * @version 3.0.5
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
?>

<script type="text/javascript">
<!--
	function insert_keyword($keyword) {
		$("jform_meta_description").value += " " + $keyword;
	}

	function include_description() {
		$("jform_meta_description").value = "<?php echo JText::_( 'COM_JEM_META_DESCRIPTION_STANDARD' ); ?>";
	}
-->
</script>

	<fieldset class="form-vertical">
		<legend><?php echo JText::_('COM_JEM_META_HANDLING'); ?></legend>
		
		<div class="control-group">
		<div class="control-label">
			<label id="jform_meta_keywords-lbl" class="hasTooltip" title="<?php $tooltip = JText::_('COM_JEM_META_KEYWORDS').'::'.JText::_('COM_JEM_META_KEYWORDS_DESC');echo JHtml::tooltipText($tooltip,'',true);?>">
					<?php echo JText::_('COM_JEM_META_KEYWORDS'); ?>
			</label>
		</div>
				<div class="controls">
				<div style="display: inline-block;">
					<?php
						// TODO use jforms here
						$meta_key = explode(", ", $this->data->meta_keywords);
					?>
					<select name="meta_keywords[]" multiple="multiple" size="5" class="inputbox" id="jform_meta_keywords">
						<option value="[title]" <?php if(in_array("[title]",$meta_key)) { echo "selected=\"selected\""; } ?>>
						<?php echo JText::_('COM_JEM_EVENT_TITLE'); ?></option>
						<option value="[a_name]" <?php if(in_array("[a_name]",$meta_key)) { echo "selected=\"selected\""; } ?>>
						<?php echo JText::_('COM_JEM_VENUE'); ?></option>
						<!-- <option value="[locid]" <?php if(in_array("[locid]",$meta_key)) { echo "selected=\"selected\""; } ?>>
						<?php echo JText::_('COM_JEM_CITY'); ?></option> -->
						<option value="[dates]" <?php if(in_array("[dates]",$meta_key)) { echo "selected=\"selected\""; } ?>>
						<?php echo JText::_('COM_JEM_DATE'); ?></option>
						<option value="[times]" <?php if(in_array("[times]",$meta_key)) { echo "selected=\"selected\""; } ?>>
						<?php echo JText::_('COM_JEM_EVENT_TIME'); ?></option>
						<option value="[enddates]" <?php if(in_array("[enddates]",$meta_key)) { echo "selected=\"selected\""; } ?>>
						<?php echo JText::_('COM_JEM_ENDDATE'); ?></option>
						<option value="[endtimes]" <?php if(in_array("[endtimes]",$meta_key)) { echo "selected=\"selected\""; } ?>>
						<?php echo JText::_('COM_JEM_END_TIME'); ?></option>
					</select>
				</div>
			</div>
			</div>
			
			<div class="control-group">
				
				<div class="control-label">
			<?php echo $this->form->getLabel('meta_description'); ?>
			</div>
			<div class="controls">
				<div style="display: inline-block;">
				<p>
					<input class="btn" type="button" onclick="insert_keyword('[title]')" value="<?php echo JText::_( 'COM_JEM_EVENT_TITLE' ); ?>" />
					<input class="btn" type="button" onclick="insert_keyword('[a_name]')" value="<?php echo JText::_( 'COM_JEM_VENUE' ); ?>" />
					<input class="btn" type="button" onclick="insert_keyword('[dates]')" value="<?php echo JText::_( 'COM_JEM_DATE' ); ?>" />
					<input class="btn" type="button" onclick="insert_keyword('[times]')" value="<?php echo JText::_( 'COM_JEM_EVENT_TIME' ); ?>" />
					<input class="btn" type="button" onclick="insert_keyword('[enddates]')" value="<?php echo JText::_( 'COM_JEM_ENDDATE' ); ?>" />
					<input class="btn" type="button" onclick="insert_keyword('[endtimes]')" value="<?php echo JText::_( 'COM_JEM_END_TIME' ); ?>" />
					</p>
					<?php echo $this->form->getInput('meta_description'); ?>
					<br/>
					<input class="btn" type="button" value="<?php echo JText::_('COM_JEM_META_DESCRIPTION_BUTTON'); ?>" onclick="include_description()" />
					&nbsp;
					<span class="hasTooltip" title="<?php $tooltip = JText::_('COM_JEM_WARNING').'::'.JText::_('COM_JEM_META_DESCRIPTION_WARN');echo JHtml::tooltipText($tooltip,'',true);?>">
						<?php echo $this->WarningIcon(); ?>
					</span>
				</div>
			</div>
		</div>
	</fieldset>
	
<br />