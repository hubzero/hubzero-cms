/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	if (GBrowserIsCompatible()) {
		

		map = new GMap2(div);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.addControl(new GOverviewMapControl());

		map.setCenter(new GLatLng(div.getAttribute('data-lat'), div.getAttribute('data-long')), div.getAttribute('data-zoom'), G_HYBRID_MAP);

		GEvent.addListener(map, 'click', function(overlay, point) {
			if (overlay) {     // marker clicked
				overlay.openInfoWindowHtml(overlay.infowindow);   // open InfoWindow
			} else if (point) {

			} // background clicked
		});

		getMarkers(div);
	}

	if (GBrowserIsCompatible()) {
		var div = document.getElementById('div_map');

		map = new GMap2(div);
		map.addControl(new GLargeMapControl());
		map.setCenter(new GLatLng(25.4091, -28.8592), 3, G_PHYSICAL_MAP);

		getMarkers(div);
	}
});

function getMarkers(div)
{
	var urlstr = div.getAttribute('data-url');
	var icon = div.getAttribute('data-path') + '/assets/img/marker_red.png';
	var request = GXmlHttp.create();
	request.open("GET", urlstr , true); // request XML from PHP with AJAX call
	request.onreadystatechange = function () {
		if (request.readyState == 4) {
			var xmlDoc = request.responseXML;
			locations = xmlDoc.documentElement.getElementsByTagName("location");
			markers = [];
			if (locations.length) {
				for (var i = 0; i < locations.length; i++) { // cycle thru locations
					// var bot = locations[i].getAttribute("bot");
					// var user = locations[i].getAttribute("user");
					var type = locations[i].getAttribute("type");
					var icon = new GIcon();
					icon.image = div.getAttribute('data-path') + '/assets/img/markerU.png';

					icon.iconSize = new GSize(18, 30);
					icon.iconAnchor = new GPoint(6, 20);
					icon.infoWindowAnchor = new GPoint(5, 1);
					markers[i] = new GMarker(new GLatLng(locations[i].getAttribute("lat"),locations[i].getAttribute("lng")),icon);
					// Add attributes to the marker so we can poll them later.
					// When clicked, an overlay will have these properties.
					// markers[i].infowindow = locations[i].getAttribute("domain")+":&nbsp;"+locations[i].getAttribute("hits")+" simulation jobs";
					markers[i].infowindow = type+"&nbsp;("+locations[i].getAttribute("type")+")";
					// Useful things to store on a marker (Not needed for this example, could be removed)
					// Tells you what index in the markers[] array an overlay is
					markers[i].markerindex = i;
					// Store the location_id of the location the marker represents.
					// Very useful to know the true id of a marker, you could then make
					// AJAX calls to the database to update the information if you had it"s location_id
					// changed location_id to ip
					markers[i].db_id = locations[i].getAttribute("type");
					map.addOverlay(markers[i]);
				}
			}
		}
	}
	request.send(null);
}