/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	if (GBrowserIsCompatible()) {
		var div = document.getElementById('map_canvas');

		// this line below as suggested by google documentation does not work!
		map = new GMap2(div);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.addControl(new GOverviewMapControl());
		map.setCenter(new GLatLng(40.4091, -86.8592), 6, G_HYBRID_MAP);

		clusterer = new Clusterer(map);

		// This code below was to get the co-ordinates of the center of the map. doesn\'t work. look into it later.
		GEvent.addListener(map, "moveend", function() {
			var center = map.getCenter();
			document.getElementById("message").innerHTML = center.toString();
		});

		getMarkers(div);

		GEvent.addListener(map, "click", function(overlay, point) {
			if (overlay) {     // marker clicked
				overlay.openInfoWindowHtml(overlay.infowindow);   // open InfoWindow
			} else if (point) {
				// background clicked
			}
		});
	}
});

function getMarkers(div)
{
	var urlstr = div.getAttribute('data-url');
	var icon = div.getAttribute('data-path') + '/assets/img/marker_red.png';
	var request = GXmlHttp.create();
	request.open('GET', urlstr , true); // request XML from PHP with AJAX call
	request.onreadystatechange = function () {
		if (request.readyState == 4) {
			var xmlDoc = request.responseXML;
			locations = xmlDoc.documentElement.getElementsByTagName("location");
			markers = [];
			if (locations.length) {
				for (var i = 0; i < locations.length; i++) { // cycle thru locations
					var usage = locations[i].getAttribute("hits");
					var icon = new GIcon();
					if ( usage > 0 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/11.png';
					}
					if ( usage > 2 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/10.png';
					}
					if ( usage > 10 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/9.png';
					}
					if ( usage > 50 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/8.png';
					}
					if ( usage > 100 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/7.png';
					}
					if ( usage > 250 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/6.png';
					}
					if ( usage > 500 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/5.png';
					}
					if ( usage > 1000 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/4.png';
					}
					if ( usage > 5000 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/3.png';
					}
					if ( usage > 10000 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/2.png';
					}
					if ( usage > 25000 ) {
						icon.image = div.getAttribute('data-path') + '/assets/img/1.png';
					}

					icon.iconSize = new GSize(20,34);
					icon.iconAnchor = new GPoint(10,34);
					icon.infoWindowAnchor = new GPoint(5, 1);

					markers[i] = new GMarker(new GLatLng(locations[i].getAttribute("lat"), locations[i].getAttribute("lng")), icon);

					// Add attributes to the marker so we can poll them later.
					// When clicked, an overlay will have these properties.
					markers[i].infowindow = locations[i].getAttribute("domain");

					// Useful things to store on a marker (Not needed for this example, could be removed)
					// Tells you what index in the markers[] array an overlay is
					markers[i].markerindex = i;
					// Store the location_id of the location the marker represents.
					// Very useful to know the true id of a marker, you could then make
					// AJAX calls to the database to update the information if you had it\'s location_id
					// changed location_id to ip
					markers[i].db_id = locations[i].getAttribute("ip");
					map.addOverlay(markers[i]);
				}
			}
		}
	}

	request.send(null);
}
