/**
 * @package     hubzero-cms
 * @file        components/com_resources/models/element/geo.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Resources) {
	HUB.Resources = {};
}

//----------------------------------------------------------
// Resource Geo location
//----------------------------------------------------------

HUB.Resources.Geo = {

	initialize: function() {
		$$('.geolocation').each(function(el){
			$(el).addEvent('blur', function() {
				var field = $(this);
				//var val = $(this).value.split(' ').join('+'); // strangely enough, this is faster than replace()

				var geocoder = new google.maps.Geocoder();

				if (geocoder) {
					geocoder.geocode({ 'address': $(this).value }, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							$($(field).id + '-lat').val(results[0].geometry.location.Ya);
						    $($(field).id + '-lng').val(results[0].geometry.location.Za);
						}
					});
				}
			});
		});
	} // end initialize
}

window.addEvent('domready', HUB.Resources.Geo.initialize);