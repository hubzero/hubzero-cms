/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/*
document.addEventListener('DOMContentLoaded', function() {
	if (GBrowserIsCompatible()) {
		var div = document.getElementById('div_map');

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
});

function getMarkers(div)
{
	var urlstr  = div.getAttribute('data-map') + '/whoisonline.xml';
	var request = GXmlHttp.create();
	request.open('GET', urlstr , true); // request XML from PHP with AJAX call
	request.onreadystatechange = function () {
		if (request.readyState == 4) {
			var xmlDoc = request.responseXML;
			locations = xmlDoc.documentElement.getElementsByTagName('marker');
			markers = [];
			if (locations.length) {
				for (var i = 0; i < locations.length; i++) { // cycle thru locations
					var info = locations[i].getAttribute('info');
					var bot  = locations[i].getAttribute('bot');
					var icon = new GIcon();

					if (bot == '1') {
						icon.image = div.getAttribute('data-path') + '/assets/img/markerB.png';
					} else {
						icon.image = div.getAttribute('data-path') + '/assets/img/markerU.png';
					}
					icon.iconSize = new GSize(18, 30);
					icon.iconAnchor = new GPoint(9, 30);
					icon.infoWindowAnchor = new GPoint(9, 15);

					markers[i] = new GMarker(new GLatLng(locations[i].getAttribute('lat'), locations[i].getAttribute('lng')), icon);

					var lat  = locations[i].getAttribute('lat');
					var lng  = locations[i].getAttribute('lng');
					var info = locations[i].getAttribute('info');

					info = info.replace(/_br_/g,'<br/>');
					info = info.replace(/_hr_/g,'<hr/>');
					info = info.replace(/_b_/g,'<b>');
					info = info.replace(/_bb_/g,'</b>');

					markers[i].infowindow = info.replace(/_br_/g,'<br/>');
					map.addOverlay(markers[i]);

					if (lat == div.getAttribute('data-lat')
					 && lng == div.getAttribute('data-long')) {
						GEvent.trigger(markers[i], 'click');
					}
				}
			}
		}
	}
	request.send(null);
}*/
var map;

document.addEventListener('DOMContentLoaded', function() {
	var div = document.getElementById('div_map');

	var myLatlng = new google.maps.LatLng(div.getAttribute('data-lat'), div.getAttribute('data-long'));
	var myOptions = {
		zoom: parseInt(div.getAttribute('data-zoom')),
		center: myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	map = new google.maps.Map(div, myOptions);

	var urlstr= div.getAttribute('data-url');

	downloadUrl(urlstr, function(data) {
		var markers = data.documentElement.getElementsByTagName("marker");
		for (var i = 0; i < markers.length; i++) {
			var latlng = new google.maps.LatLng(parseFloat(markers[i].getAttribute("lat")), parseFloat(markers[i].getAttribute("lng")));
			var marker = createMarker(markers[i].getAttribute("info"), latlng);
		}
	});
});

function createMarker(info, latlng) {
	var infowindow;

	info = info.replace(/_br_/g, '<br/>');
	info = info.replace(/_hr_/g, '<hr/>');
	info = info.replace(/_b_/g, '<b>');
	info = info.replace(/_bb_/g, '</b>');

	var marker = new google.maps.Marker({position: latlng, map: map});

	google.maps.event.addListener(marker, "click", function() {
		if (infowindow) {
			infowindow.close();
		}
		infowindow = new google.maps.InfoWindow({content: info});
		infowindow.open(map, marker);
	});

	return marker;
}
