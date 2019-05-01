/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function($){
	$('.geolocation').on('blur', function() {
		var field = $(this),
			geocoder = new google.maps.Geocoder();

		if (geocoder) {
			geocoder.geocode({ 'address': $(this).val() }, function (results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					$('#' + $(field).attr('id') + '-lat').val(results[0].geometry.location.lat()); //Ya
					$('#' + $(field).attr('id') + '-lng').val(results[0].geometry.location.lng()); //Za
				}
			});
		}
	});
});