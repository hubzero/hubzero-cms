/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	var div = document.getElementById('map_canvas');

	var dt = div.getAttribute('data-date');
	var plotdt = div.getAttribute('data-plotdt');

	if (GBrowserIsCompatible()) {
		map = new GMap2(div);
		map.addControl(new GSmallMapControl());
		map.setCenter(new GLatLng(35, -113), 5, G_SATELLITE_MAP);
		map.setCenter(new GLatLng(35, -113), 5, G_HYBRID_MAP);

		var icon1 = new GIcon();
		icon1.image = div.getAttribute('data-path') + '/assets/img/org.png';
		icon1.iconSize = new GSize(40, 40);
		icon1.iconAnchor = new GPoint(20, 20);
		marker1 = new GMarker(new GLatLng('40.4427', '-86.9237'), icon1);
		map.addOverlay(marker1);

		getMarkers(dt, div);

		var label = new ELabel(new GLatLng(-52.7, 11.0), plotdt, 'style1');
		map.addOverlay(label);
	}
});

function getMarkers(dt, div) {
	var urlstr = div.getAttribute('data-url') + '&period=' + dt;
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
					icon.image = div.getAttribute('data-path') + '/assets/images/1.png';
					icon.iconSize = new GSize(20, 34);
					icon.iconAnchor = new GPoint(10, 34);
					markers[i] = new GMarker(new GLatLng(locations[i].getAttribute('lat'), locations[i].getAttribute('lng')), icon);
					map.addOverlay(markers[i]);
				}
			}
		}
	}
	request.send(null);
}
