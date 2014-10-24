<?php
/**
 * @version 3.0.3
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal', 'a.flyermodal');

// Create shortcut to parameters.
$params		= $this->params;
$settings	= json_decode($this->item->attribs);
?>

<script type="text/javascript">
	window.addEvent('domready', function(){

		checkmaxplaces();
		starter("<?php echo JText::_ ('COM_JEM_META_ERROR'); ?>",$("jform_meta_keywords").value,$("jform_meta_description").value);
		
		$('jform_meta_keywords').addEvents({
	    	focus: function(){
	    		get_inputbox('jform_meta_keywords');
	    	},
	    	blur: function(){
	        	change_metatags;
	    	}
		});

		$('jform_meta_description').addEvents({
	    	focus: function(){
	    		get_inputbox('jform_meta_description');
	    	},
	    	blur: function(){
	        	change_metatags;
	    	}
		});
	});

	function checkmaxplaces(){
		var maxplaces = $('jform_maxplaces');

		if (maxplaces != null){
			$('jform_maxplaces').addEvent('change', function(){
				if ($('event-available')) {
					var val = parseInt($('jform_maxplaces').value);
					var booked = parseInt($('event-booked').value);
					$('event-available').value = (val-booked);
				}
			});

			$('jform_maxplaces').addEvent('keyup', function(){
				if ($('event-available')) {
					var val = parseInt($('jform_maxplaces').value);
					var booked = parseInt($('event-booked').value);
					$('event-available').value = (val-booked);
				}
			});
		}
	}
</script>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'editevent.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<!-- container -->
<div id="jem" class="jem_editevent<?php echo $this->pageclass_sfx; ?>">

	<div class="span12">
<!-- start form -->
		<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_jem&a_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
			

<!-- Buttons -->
<div class="topbox">
	<div class="button_flyer">
		<div class="btn-toolbar">
			<?php if (JFactory::getUser()->authorise('core.manage', 'com_jem')) { ?>
				<button type="button" class="btn btn-small btn-success" onclick="Joomla.submitbutton('editevent.apply')"><span class="icon-apply icon-white"></span><?php echo ' '.JText::_('JTOOLBAR_APPLY') ?></button>
				<button type="button" class="btn btn-small" onclick="Joomla.submitbutton('editevent.save')"><span class="icon-save"></span><?php echo ' '.JText::_('JTOOLBAR_SAVE') ?></button>
			<?php } else { ?>
				<button type="button" class="btn btn-small btn-success" onclick="Joomla.submitbutton('editevent.save')"><span class="icon-apply icon-white"></span><?php echo ' '.JText::_('JSAVE') ?></button>
			<?php } ?>
			<button type="button" class="btn btn-small" onclick="Joomla.submitbutton('editevent.cancel')"><span class="icon-cancel icon-red"></span><?php echo ' '.JText::_('JCANCEL') ?></button>
		</div>
	</div>
</div>
<div class="clearfix"> </div>

<!-- page_heading -->
		<?php if ($params->get('show_page_heading')) : ?>
		<h1>
			<?php echo $this->escape($params->get('page_heading')); ?>
		</h1>
		<?php endif; ?>
			
			<div class="clearfix"></div>
			
			<?php if ($this->params->get('showintrotext')) : ?>
			<div class="description no_space floattext">
				<?php echo $this->params->get('introtext'); ?>
			</div>
			<?php endif; ?>
			<p>&nbsp;</p>

<!-- recurrence-message, above the tabs -->		
			
	<?php if ($this->item->recurrence_groupcheck) { ?>
	<fieldset class="form-horizontal alert">
		
		<div class="description">
		<div style="float:left;">
			<?php echo JemOutput::recurrenceicon($this->item, false, false); ?>
		</div>
		<div class="floattext" style="margin-left:36px;">
			<strong><?php echo JText::_('COM_JEM_EDITEVENT_WARN_RECURRENCE_TITLE'); ?></strong>
			<br>
			<?php
				echo nl2br(JText::_('COM_JEM_EDITEVENT_WARN_RECURRENCE_TEXT'));
			?>
			<br><br>
			<button class="btn" type="button" value="<?php echo JText::_('COM_JEM_EDITEVENT_RECURRENCE_REMOVEFROMSET');?>" onclick="Joomla.submitbutton('editevent.removefromset')"><?php echo JText::_('COM_JEM_EDITEVENT_RECURRENCE_REMOVEFROMSET');?></button>
		</div>
		</div>		
	</fieldset>
	<?php } ?>			
					
<!-- TABS -->
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JEM_EDITEVENT_INFO_TAB', true)); ?>	

			<fieldset class="form-horizontal">
				<legend><span class="legendcolor"><?php echo JText::_('COM_JEM_EDITEVENT_DETAILS_LEGEND'); ?></span></legend>
					<?php
						echo $this->form->renderField('title');
						if (is_null($this->item->id)):
							echo $this->form->renderField('alias');
					 	endif; 
						echo $this->form->renderField('dates');
						echo $this->form->renderField('enddates');
						echo $this->form->renderField('times');
						echo $this->form->renderField('endtimes');
						echo $this->form->renderField('cats');
						if ($this->settings->get('editevent_show_featured',1) && !($this->valguest)) { 
							echo $this->form->renderField('featured');
						}
						if ($this->settings->get('editevent_show_published',1) && !($this->valguest)) { 
							echo $this->form->renderField('published');
						}
						echo $this->form->renderField('locid');
						if (!$this->valguest) { 
							 echo $this->form->renderField('contactid'); 
						} 
						echo $this->form->renderField('captcha'); 
						echo $this->form->renderField('mathquiz'); 
						echo $this->form->renderField('mathquiz_answer'); 
					?>
			</fieldset>
			
			
			<fieldset class="form-vertical">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('articletext'); ?></div>
					<br>
					<div class="controls"><?php echo $this->form->getInput('articletext'); ?></div>
				</div>
			</fieldset>
			

			<!-- START META FIELDSET -->
			<fieldset class="form-horizontal">
				<legend><span class="legendcolor"><?php echo JText::_('COM_JEM_META_HANDLING'); ?></span></legend>
					<p>
						<input class="btn" type="button" onclick="insert_keyword('[title]')" value="<?php echo JText::_('COM_JEM_TITLE');	?>" />
						<input class="btn" type="button" onclick="insert_keyword('[a_name]')" value="<?php	echo JText::_('COM_JEM_VENUE');?>" />
						<input class="btn" type="button" onclick="insert_keyword('[categories]')" value="<?php	echo JText::_('COM_JEM_CATEGORIES');?>" />
						<input class="btn" type="button" onclick="insert_keyword('[dates]')" value="<?php echo JText::_('COM_JEM_DATE');?>" />
						<input class="btn" type="button" onclick="insert_keyword('[times]')" value="<?php echo JText::_('COM_JEM_TIME');?>" />
						<input class="btn" type="button" onclick="insert_keyword('[enddates]')" value="<?php echo JText::_('COM_JEM_ENDDATE');?>" />
						<input class="btn" type="button" onclick="insert_keyword('[endtimes]')" value="<?php echo JText::_('COM_JEM_ENDTIME');?>" />
					</p>
					<?php echo $this->form->renderField('meta_keywords'); ?>
					<?php echo $this->form->renderField('meta_description'); ?>
			</fieldset>
			<!--  END META FIELDSET -->

			
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			<?php 
			if ($this->settings->get('editevent_show_attachmentstab',1) && !($this->valguest)) {
				echo JHtml::_('bootstrap.addTab', 'myTab', 'attachments', JText::_('COM_JEM_EVENT_ATTACHMENTS_TAB', true));
				echo $this->loadTemplate('attachments'); 
				echo JHtml::_('bootstrap.endTab');
			}
			?>
		
			<?php
			if ($this->settings->get('editevent_show_othertab',1) && !($this->valguest)) { 
				echo JHtml::_('bootstrap.addTab', 'myTab', 'other', JText::_('COM_JEM_EVENT_OTHER_TAB', true)); 
				echo $this->loadTemplate('other'); 
				echo JHtml::_('bootstrap.endTab');
			}
			?>
		
			<?php 
			echo JHtml::_('bootstrap.endTabSet'); 
			?>

					
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
			<input type="hidden" name="author_ip" value="<?php echo $this->item->author_ip; ?>" />
			<input type="hidden" name="recurrence_check" value="<?php echo $this->item->recurrence_groupcheck; ?>" />
			<input type="hidden" name="recurrence_group" value="<?php echo $this->item->recurrence_group; ?>" />
			<input type="hidden" name="recurrence_country_holidays" value="<?php echo $this->item->recurrence_country_holidays; ?>" />
			<?php if($this->params->get('enable_category', 0) == 1) :?>
			<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>"/>
			<?php endif;?>
			<?php echo $this->form->renderField('timeout'); ?>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>