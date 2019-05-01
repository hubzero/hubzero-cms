/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Publications) {
	HUB.Publications = {};
}

//----------------------------------------------------------
// Publication Geo location
//----------------------------------------------------------

HUB.Publications.Geo = {
//610 Purdue Mall  West Lafayette, IN 47907
	initialize: function() {
		$('.geolocation').each(function(i, el){
			$(el).on('blur', function() {
				var field = $(this);
				//var val = $(this).val().split(' ').join('+'); // strangely enough, this is faster than replace()
				/*$.getJSON("https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=" + val + "&callback=?", function(data){
					console.log(data);
					if (data.status == 'OK') {
						$($(field).attr('id') + '-lat').val(data.results.geometry.location.lat);
					    $($(field).attr('id') + '-lng').val(data.results.geometry.location.lng);
					}
				});*/

				var geocoder = new google.maps.Geocoder();

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
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Publications.Geo.initialize();
});