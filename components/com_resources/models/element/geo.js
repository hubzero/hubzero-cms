/**
 * @package     hubzero-cms
 * @file        components/com_resources/models/element/geo.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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