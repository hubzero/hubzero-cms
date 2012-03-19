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
			el.addEvent('blur', function() {
				var field = $(this);
				var val = $(this).value.split(' ').join('+'); // strangely enough, this is faster than replace()
				var jSonRequest = new Json.Remote("https://maps.googleapis.com/maps/api/geocode/json?sensor=true_or_false&address=" + val, {onComplete: function(response){
					if (response.status == 'OK') {
						$($(field).id+'-lat').value = response.results.geometry.location.lat;
					    $($(field).id+'-lng').value = response.results.geometry.location.lng;
					}
				}}).send();
			});
		});
	} // end initialize
}

window.addEvent('domready', HUB.Resources.Geo.initialize);