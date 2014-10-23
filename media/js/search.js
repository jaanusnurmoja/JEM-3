/**
 * @version 3.0.3
 * @package JEM
 * @copyright (C) 2013-2014 joomlaeventmanager.net
 * @copyright (C) 2005-2009 Christoph Lukes
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @author Sascha Karnatz
 */

window.addEvent('domready', function(){
	
	if ($('filter_date_from_img')) {
		$('filter_date_from_img').addEvent('change', function() {
				this.form.submit();
	});
	}
	
	if ($('filter_date_to_img')) {
		$('filter_date_to_img').addEvent('change', function() {
				this.form.submit();
	});
	}

	if ($('filter_continent')) {
		$('filter_continent').addEvent('change', function() {
			if (country = $('filter_country')) {
				country.selectedIndex = 0;
			}
			if (city = $('filter_city')) {
				city.selectedIndex = 0;
			}
			this.form.submit();
		});
	}

	if (country = $('filter_country')) {
		country.addEvent('change', function() {
			if (city = $('filter_city')) {
				city.selectedIndex = 0;
			}
			this.form.submit();
		});
	}

	if (city = $('filter_city')) {
		city.addEvent('change', function() {
			this.form.submit();
		});
	}

	if ($('filter_category')) {
		$('filter_category').addEvent('change', function() {
			this.form.submit();
		});
	}
});