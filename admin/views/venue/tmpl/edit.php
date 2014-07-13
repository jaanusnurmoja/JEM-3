<?php
/**
 * @version 3.0.1
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal', 'a.modal');

// Create shortcut to parameters.
$params = $this->state->get('params');
$params = $params->toArray();

# defining values for centering default-map
$location = JemHelper::defineCenterMap($this->form);
$mapType = $this->mapType;

// Define slides options
$slidesOptions = array(
		"useCookie" => "1"
);
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'venue.cancel' || document.formvalidator.isValid(document.id('venue-form'))) {
			Joomla.submitform(task, document.getElementById('venue-form'));
		}
	}

	window.addEvent('domready', function() {		
		setAttribute();
		test();
	});

	function setAttribute(){
		document.getElementById("tmp_form_postalCode").setAttribute("geo-data", "postal_code");
		document.getElementById("tmp_form_city").setAttribute("geo-data", "locality");
		document.getElementById("tmp_form_state").setAttribute("geo-data", "administrative_area_level_1");
		document.getElementById("tmp_form_street").setAttribute("geo-data", "street_address");
		document.getElementById("tmp_form_route").setAttribute("geo-data", "route");
		document.getElementById("tmp_form_streetnumber").setAttribute("geo-data", "street_number");
		document.getElementById("tmp_form_country").setAttribute("geo-data", "country_short");
		document.getElementById("tmp_form_latitude").setAttribute("geo-data", "lat");
		document.getElementById("tmp_form_longitude").setAttribute("geo-data", "lng");
		document.getElementById("tmp_form_venue").setAttribute("geo-data", "name");	
	}

	function meta(){
		var f = document.getElementById('venue-form');
		if(f.jform_meta_keywords.value != "") f.jform_meta_keywords.value += ", ";
		f.jform_meta_keywords.value += f.jform_venue.value+', ' + f.jform_city.value;
	}

	function test(){			
			var form = document.getElementById('venue-form');
			var map = $('jform_map');
			var streetcheck = $(form.jform_street).hasClass('required');

			if(map && map.checked == true) {
				var lat = $('jform_latitude');
				var lon = $('jform_longitude');

				if(lat.value == ('' || 0.000000) || lon.value == ('' || 0.000000)) {
					if(!streetcheck) {
						addrequired();
					}
				} else {
					if(lat.value != ('' || 0.000000) && lon.value != ('' || 0.000000) ) {
						removerequired();
					}
				}
				$('mapdiv').show();
			}

			if(map && map.checked == false) {
				removerequired();
				$('mapdiv').hide();
			}
	}

	function addrequired() {
		var form = document.getElementById('venue-form');

		$(form.jform_street).addClass('required');
		$(form.jform_postalCode).addClass('required');
		$(form.jform_city).addClass('required');
		$(form.jform_country).addClass('required');
	}

	function removerequired() {
		var form = document.getElementById('venue-form');

		$(form.jform_street).removeClass('required');
		$(form.jform_postalCode).removeClass('required');
		$(form.jform_city).removeClass('required');
		$(form.jform_country).removeClass('required');
	}


	jQuery(function() {
		jQuery("#geocomplete").geocomplete({
			map: ".map_canvas",
			<?php echo $location; ?>
			details: "form ",
			detailsAttribute: "geo-data",
			types: ['establishment', 'geocode'],
			mapOptions: {
			      zoom: 16,
			      <?php echo 'mapTypeId:'.$mapType; ?>
			    },
			markerOptions: {
				draggable: true
			}
			
		});

		jQuery("#geocomplete").bind('geocode:result', function(){
				var street = jQuery("#tmp_form_street").val();
				var route  = jQuery("#tmp_form_route").val();
				
				if (route) {
					/* something to add */
				} else {
					jQuery("#tmp_form_street").val('');
				}
		});

		jQuery("#geocomplete").bind("geocode:dragged", function(event, latLng){
			jQuery("#tmp_form_latitude").val(latLng.lat());
			jQuery("#tmp_form_longitude").val(latLng.lng());
		});

		/* option to attach a reset function to the reset-link
			jQuery("#reset").click(function(){
			jQuery("#geocomplete").geocomplete("resetMarker");
			jQuery("#reset").hide();
			return false;
		});
		*/

		jQuery("#find-left").click(function() {
			jQuery("#geocomplete").val(jQuery("#jform_street").val() + ", " + jQuery("#jform_postalCode").val() + " " + jQuery("#jform_city").val());
			jQuery("#geocomplete").trigger("geocode");
		});

		jQuery("#cp-latlong").click(function() {
			document.getElementById("jform_latitude").value = document.getElementById("tmp_form_latitude").value;
			document.getElementById("jform_longitude").value = document.getElementById("tmp_form_longitude").value;
			test();
		});

		jQuery("#cp-address").click(function() {
			document.getElementById("jform_street").value = document.getElementById("tmp_form_street").value;
			document.getElementById("jform_postalCode").value = document.getElementById("tmp_form_postalCode").value;
			document.getElementById("jform_city").value = document.getElementById("tmp_form_city").value;
			document.getElementById("jform_state").value = document.getElementById("tmp_form_state").value;	
			document.getElementById("jform_country").value = document.getElementById("tmp_form_country").value;
		});

		jQuery("#cp-venue").click(function() {
			var venue = document.getElementById("tmp_form_venue").value;
			if (venue) {
				document.getElementById("jform_venue").value = venue;
			}
		});

		jQuery("#cp-all").click(function() {
			jQuery("#cp-address").click();
			jQuery("#cp-latlong").click();
			jQuery("#cp-venue").click();
		});	

		jQuery('#jform_map').on('keyup keypress blur change', function() {
		    test();
		});

		jQuery('#jform_latitude').on('keyup keypress blur change', function() {
		    test();
		});

		jQuery('#jform_longitude').on('keyup keypress blur change', function() {
		    test();
		});
	});
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_jem&layout=edit&id='.(int) $this->item->id); ?>"
	class="form-validate" method="post" name="adminForm" id="venue-form" enctype="multipart/form-data">
<div class="form-horizontal">
<div class="span12">

<!-- Tabs -->	
	<div class="span8">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'venue-tab1','useCookie' => '1')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'venue-tab1', JText::_('COM_JEM_VENUE_INFO_TAB', true)); ?>
		<fieldset class="form-horizontal">
			<legend>
				<?php echo empty($this->item->id) ? JText::_('COM_JEM_NEW_VENUE') : JText::sprintf('COM_JEM_VENUE_DETAILS', $this->item->id); ?>
			</legend>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('venue');?></div>
				<div class="controls"><?php echo $this->form->getInput('venue'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('street'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('street'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('postalCode'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('postalCode'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('city'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('city'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('country'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('country'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('latitude'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('latitude'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('longitude'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('longitude'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('url'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('url'); ?></div>
			</div>
		</fieldset>
		
		<fieldset class="form-vertical">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('locdescription'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('locdescription'); ?></div>
			</div>
		</fieldset>

		<?php echo JHtml::_('bootstrap.endTab'); ?>

<!-- Attachments -->		
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'venue-attachments', JText::_('COM_JEM_EVENT_ATTACHMENTS_TAB', true)); ?>	
		<?php echo $this->loadTemplate('attachments'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>		
		
		<?php echo JHtml::_('bootstrap.endTabSet');?>
</div>
<div class="span4">		
	
	<!--  start of sliders -->
	<?php echo JHtml::_('bootstrap.startAccordion', 'venue-sliders-'.$this->item->id, $slidesOptions); ?>

<!-- Publishing -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_FIELDSET_PUBLISHING'), 'venue-publishing'); ?>
		<fieldset class="form-vertical">
			
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>

			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
			</div>
			
		<?php foreach($this->form->getFieldset('publish') as $field): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
		<?php endforeach; ?>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
	
		
<!-- CUSTOM -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_CUSTOMFIELDS'), 'venue-custom'); ?>
		<fieldset class="form-vertical">
				<?php foreach($this->form->getFieldset('custom') as $field): ?>
					<div class="control-group">
						<div class="control-label"><?php echo $field->label; ?></div>
						<div class="controls"><?php echo $field->input; ?></div>
					</div>
				<?php endforeach; ?>
		</fieldset>		
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
	
		
<!-- IMAGE -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_IMAGE'), 'venue-image'); ?>
		<fieldset class="form-vertical">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('locimage'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('locimage'); ?></div>
			</div>
		</fieldset>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
		
		
<!-- Meta -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_METADATA_INFORMATION'), 'venue-meta'); ?>
		<fieldset class="form-vertical">
			<input type="button" class="btn" value="<?php echo JText::_('COM_JEM_ADD_VENUE_CITY'); ?>" onclick="meta()" />
				<?php foreach($this->form->getFieldset('meta') as $field): ?>
				<div class="control-group">
					<div class="control-label"><?php echo $field->label; ?></div>
					<div class="controls"><?php echo $field->input; ?></div>
				</div>
				<?php endforeach; ?>
			
		</fieldset>
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
		

<!-- Geodata -->
	<?php echo JHtml::_('bootstrap.addSlide', 'venue-sliders-'.$this->item->id, JText::_('COM_JEM_FIELDSET_GEODATA'), 'venue-geodata'); ?>
	
		<fieldset class="form-vertical" id="geodata">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('map'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('map'); ?></div>
			</div>
			
			<div class="clr"></div>
			<div id="mapdiv">
				<input id="geocomplete" type="text" size="55" placeholder="<?php echo JText::_( 'COM_JEM_VENUE_ADDRPLACEHOLDER' ); ?>" value="" />
				<input id="find-left" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_ADDR_FINDVENUEDATA');?>" />
				<div class="clr"></div>
				<div class="map_canvas"></div>

				
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_STREET'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_street" /></div>
						<input type="hidden" class="readonly" id="tmp_form_streetnumber" readonly="readonly" />
						<input type="hidden" class="readonly" id="tmp_form_route" readonly="readonly" />
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_ZIP'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_postalCode" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_CITY'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_city"/></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_STATE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_state" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_VENUE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_venue" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_COUNTRY'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_country" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_LATITUDE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_latitude" /></div>
				</div>
				<div class="control-group">
					<div class="control-label"><label><?php echo JText::_('COM_JEM_LONGITUDE'); ?></label></div>
					<div class="controls"><input type="text" disabled="disabled" class="readonly" id="tmp_form_longitude" /></div>
				</div>
				
				<div class="clr"></div>
				<input id="cp-all" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_DATA'); ?>" />
				<input id="cp-address" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_ADDRESS'); ?>" />
				<input id="cp-venue" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_VENUE'); ?>" />
				<input id="cp-latlong" class="btn" type="button" value="<?php echo JText::_('COM_JEM_VENUE_COPY_COORDINATES'); ?>" />
			</div>
		</fieldset>
	
		<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="author_ip" value="<?php echo $this->item->author_ip; ?>" />
		<?php echo JHtml::_('form.token'); ?>
		</div></div>
	</div>
</form>