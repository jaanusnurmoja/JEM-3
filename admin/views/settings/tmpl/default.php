<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * * @todo: move js to a file
 */

defined('_JEXEC') or die;

// Load tooltips behavior
JHtml::_('behavior.formvalidation');
//JHtml::_('behavior.switcher');
JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.framework');
JHtml::_('behavior.modal');
?>

<script>
window.addEvent('domready', function(){

	$('jform_showcity0').addEvent('click', cityoff);
	$('jform_showcity1').addEvent('click', cityon);

	if($('jform_showcity1').checked) {
		cityon();
	}

	$('jform_showatte0').addEvent('click', atteoff);
	$('jform_showatte1').addEvent('click', atteon);

	if($('jform_showatte1').checked) {
		atteon();
	}

	$('jform_showtitle0').addEvent('click', titleoff);
	$('jform_showtitle1').addEvent('click', titleon);

	if($('jform_showtitle1').checked) {
		titleon();
	}

	$('jform_showlocate0').addEvent('click', locoff);
	$('jform_showlocate1').addEvent('click', locon);

	if($('jform_showlocate1').checked) {
		locon();
	}

	$('jform_showcat0').addEvent('click', catoff);
	$('jform_showcat1').addEvent('click', caton);

	if($('jform_showcat1').checked) {
		caton();
	}

	$('jform_showeventimage0').addEvent('click', evimageoff);
	$('jform_showeventimage1').addEvent('click', evimageon);

	if($('jform_showeventimage1').checked) {
		evimageon();
	}

	$('jform_gddisabled0').addEvent('click', lboff);
	$('jform_gddisabled1').addEvent('click', lbon);

	if($('jform_gddisabled1').checked) {
		lbon();
	}

	$("jform_globalattribs_event_show_mapserv").addEvent('change', testmap);

	var mapserv = $("jform_globalattribs_event_show_mapserv");
	var nrmapserv = mapserv.options[mapserv.selectedIndex].value;

	if (nrmapserv == 1 || nrmapserv == 2) {
		eventmapon();
	} else {
		eventmapoff();
	}


	$("jform_globalattribs_global_show_mapserv").addEvent('change', testmap);

	var mapserv = $("jform_globalattribs_global_show_mapserv");
	var nrmapserv = mapserv.options[mapserv.selectedIndex].value;

	if (nrmapserv == 1 || nrmapserv == 2) {
		globalmapon();
	} else {
		globalmapoff();
	}

	$("jform_oldevent").addEvent('change', testevhandler);

	var evhandler = $("jform_oldevent");
	var nrevhandler = evhandler.options[evhandler.selectedIndex].value;

	if (nrevhandler == 1 || nrevhandler == 2) {
		evhandleron();
	} else {
		evhandleroff();
	}

	$('jform_globalattribs_event_comunsolution').addEvent('change', testcomm);

	var commhandler = $("jform_globalattribs_event_comunsolution");
	var nrcommhandler = commhandler.options[commhandler.selectedIndex].value;

	if (nrcommhandler == 1) {
		common();
	} else {
		commoff();
	}


	var ObjArray = $$('input.colorpicker').get('id').sort();

	var arrayLength = ObjArray.length;
	for (var i = 0; i < arrayLength; i++) {
	    var Obj 	= $(ObjArray[i]);
		var color = testcolor(Obj.value);
		if (color) {
			Obj.style.color = color;
		}
	}
});


function testcolor(color) {
	if(color.length==7)
	{
		color=color.substring(1);
	}
	var R = parseInt(color.substring(0,2),16);
	var G = parseInt(color.substring(2,4),16);
	var B = parseInt(color.substring(4,6),16);
	var x = Math.sqrt(R * R * .299 + G * G * .587 + B * B * .114);

	var sColorText = x < 130 ? '#FFFFFF' : '#000000';

	return sColorText;
	}

function testcomm()
{
	var commhandler = $("jform_globalattribs_event_comunsolution");
	var nrcommhandler = commhandler.options[commhandler.selectedIndex].value;

	if (nrcommhandler == 1) {
		common();
	} else {
		commoff();
	}
}

function testmap()
{
	var mapserv = $("jform_globalattribs_event_show_mapserv");
	var nrmapserv = mapserv.options[mapserv.selectedIndex].value;

	if (nrmapserv == 1 || nrmapserv == 2) {
		eventmapon();
	} else {
		eventmapoff();
	}

	var mapserv2 = $("jform_globalattribs_global_show_mapserv");
	var nrmapserv2 = mapserv2.options[mapserv2.selectedIndex].value;

	if (nrmapserv2 == 1 || nrmapserv2 == 2) {
		globalmapon();
	} else {
		globalmapoff();
	}
}

function testevhandler()
{
	var evhandler = $("jform_oldevent");
	var nrevhandler = evhandler.options[evhandler.selectedIndex].value;

	if (nrevhandler == 1 || nrevhandler == 2) {
		evhandleron();
	} else {
		evhandleroff();
	}
}

function cityon()
{
	document.getElementById('city1').style.display = '';
}

function cityoff()
{
	var citywidth = document.getElementById('jform_citywidth');
	document.getElementById('city1').style.display = 'none';
	citywidth.value='';
}

function atteon()
{
	document.getElementById('atte1').style.display = '';
}

function atteoff()
{
	var attewidth = document.getElementById('jform_attewidth');
	document.getElementById('atte1').style.display = 'none';
	attewidth.value='';
}

function titleon()
{
	document.getElementById('title1').style.display = '';
}

function titleoff()
{
	var titlewidth = document.getElementById('jform_titlewidth');
	document.getElementById('title1').style.display = 'none';
	titlewidth.value='';
}

function locon()
{
	document.getElementById('loc1').style.display = '';
	document.getElementById('loc2').style.display = '';
}

function locoff()
{
	var locatewidth = document.getElementById('jform_locationwidth');
	document.getElementById('loc1').style.display = 'none';
	locatewidth.value='';
	document.getElementById('loc2').style.display = 'none';
}

function caton()
{
	document.getElementById('cat1').style.display = '';
	document.getElementById('cat2').style.display = '';
}

function catoff()
{
	var catwidth = document.getElementById('jform_catfrowidth');
	document.getElementById('cat1').style.display = 'none';
	catwidth.value='';
	document.getElementById('cat2').style.display = 'none';
}

function evimageon()
{
	document.getElementById('evimage1').style.display = '';
}

function evimageoff()
{
	var evimagewidth = document.getElementById('jform_tableeventimagewidth');
	document.getElementById('evimage1').style.display = 'none';
	evimagewidth.value='';
}

function lbon()
{
	document.getElementById('lb1').style.display = '';
}

function lboff()
{
	document.getElementById('lb1').style.display = 'none';
}

function eventmapon()
{
	document.getElementById('eventmap1').style.display = '';
	document.getElementById('eventmap2').style.display = '';
}

function eventmapoff()
{
	document.getElementById('eventmap1').style.display = 'none';
	document.getElementById('eventmap2').style.display = 'none';
	document.getElementById('jform_globalattribs_event_tld').value = '';
	document.getElementById('jform_globalattribs_event_lg').value = '';
}

function globalmapon()
{
	document.getElementById('globalmap1').style.display = '';
	document.getElementById('globalmap2').style.display = '';
}

function globalmapoff()
{
	document.getElementById('globalmap1').style.display = 'none';
	document.getElementById('globalmap2').style.display = 'none';
	document.getElementById('jform_globalattribs_global_tld').value = '';
	document.getElementById('jform_globalattribs_global_lg').value = '';
}


function evhandleron()
{
	document.getElementById('evhandler1').style.display = '';
}

function evhandleroff()
{
	document.getElementById('evhandler1').style.display = 'none';
}

function common()
{
	document.getElementById('comm1').style.display = '';
}

function commoff()
{
	document.getElementById('comm1').style.display = 'none';
}
</script>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'settings.cancel' || document.formvalidator.isValid(document.id('settings-form'))) {
			Joomla.submitform(task, document.getElementById('settings-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jem&view=settings'); ?>" method="post" id="settings-form" name="adminForm" class="form-validate">
	<div class="row-fluid">
		<div class="span12">
			<!-- Tabs -->
			<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'settings-basic')); ?>
			
			
			
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'settings-basic', JText::_('COM_JEM_BASIC_SETTINGS', true)); ?>
			<div class="row-fluid">	
				<div class="span6">
					<?php echo $this->loadTemplate('basicdisplay'); ?>
					<?php echo $this->loadTemplate('basiceventhandling'); ?>
				</div><div class="span6">
					<?php echo $this->loadTemplate('basicimagehandling'); ?>
					<?php echo $this->loadTemplate('basicmetahandling'); ?>
				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			
			
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'settings-layout2', JText::_('COM_JEM_EVENT_PAGE', true)); ?>
			<div class="row-fluid">
				<div class="span6">		
					<?php echo $this->loadTemplate('evevents'); ?>
				</div><div class="span6">
					<?php echo $this->loadTemplate('evvenues'); ?>
					<?php echo $this->loadTemplate('evregistration'); ?>
				</div>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			
			
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'settings-layout', JText::_('COM_JEM_LAYOUT', true)); ?>
			<div class="row-fluid">
				<?php echo $this->loadTemplate('layout'); ?>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			
			
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'settings-params', JText::_('COM_JEM_GLOBAL_PARAMETERS', true)); ?>
			<div class="row-fluid">
				<?php echo $this->loadTemplate('parameters'); ?>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			
			
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'settings-usercontrol', JText::_('COM_JEM_USER_CONTROL', true)); ?>
			<div class="row-fluid">
				<?php echo $this->loadTemplate('usercontrol'); ?>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			
			
			
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'settings-configinfo', JText::_('COM_JEM_SETTINGS_TAB_CONFIGINFO', true)); ?>
			<div class="row-fluid">
				<?php echo $this->loadTemplate('configinfo'); ?>
			</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>	
				
				
				
			<?php echo JHtml::_('bootstrap.endTabSet'); ?>	
		</div>
	</div>
	
	<!-- Hidden fields -->
	<input type="hidden" name="task" value="">
	<input type="hidden" name="id" value="1">
	<input type="hidden" name="lastupdate" value="<?php $this->jemsettings->lastupdate; ?>">
	<input type="hidden" name="option" value="com_jem">
	<input type="hidden" name="controller" value="settings">
	<?php echo JHtml::_('form.token'); ?>
</form>
