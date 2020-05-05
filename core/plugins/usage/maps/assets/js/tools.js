/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	if (GBrowserIsCompatible()) {
		var div = document.getElementById('div_map');

		map = new GMap2(div);
		map.setCenter(new GLatLng(div.getAttribute('data-lat'), div.getAttribute('data-long')), div.getAttribute('data-zoom'), G_HYBRID_MAP);

		getMarkers(div);
	}
});

function getMarkers(div)
{
	var urlstr = div.getAttribute('data-map');
	var request = GXmlHttp.create();
	request.open('GET', urlstr , true); // request XML from PHP with AJAX call
	request.onreadystatechange = function () {
		if (request.readyState == 4) {
			var xmlDoc = request.responseXML;
			locations = xmlDoc.documentElement.getElementsByTagName('marker');
			markers = [];
			if (locations.length) {
				for (var i = 0; i < locations.length; i++) { // cycle thru locations
					var icon = new GIcon();
					icon.image = div.getAttribute('data-path') + '/assets/img/1.png';
					icon.iconSize = new GSize(12, 20);
					icon.iconAnchor = new GPoint(6, 20);
					markers[i] = new GMarker(new GLatLng(locations[i].getAttribute('lat'),locations[i].getAttribute('lng')),icon);
					map.addOverlay(markers[i]);
				}
			}
		}
	}

	request.send(null);
}
