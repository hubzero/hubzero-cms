<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$id = Request::getString('id', '');

if (!$id) {
	$html = 'Nothing to see here!';
} else {
	$lat = 20;
	$lng = 0;
	$zoom = 2;

$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"
	  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
 <head>
	<script type='text/javascript' src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$key."'></script>
	<script type='text/javascript' src='" . rtrim(Request::base(), '/') . "/core/plugins/usage/maps/assets/js/Clusterer2.js'> </script>
	<script type='text/javascript'>
	function load() {
		if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById('div_map'));
			//map.addControl(new GSmallMapControl());
			map.setCenter(new GLatLng(".$lat.",".$lng."), ".$zoom.", G_HYBRID_MAP);
			getMarkers();
		}
	}
	function getMarkers() {
		var urlstr='".$mappath."/resource_maps/".$id.".xml';
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
						icon.image = '" . rtrim(Request::base(), '/') . "/core/plugins/usage/maps/assets/img/1.png';
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
	</script>
	<style>
	#div_map {
		width:1024px;
		height:600px;
	}
	</style>
 </head>
 <body onload='load()' onunload='GUnload()'>
	<div id='div_map'> </div>
 </body>
</html>";
}
