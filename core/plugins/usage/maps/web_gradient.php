<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dataurl = Route::url('index.php?option='.$option.'&task='.$task.'&type='.$type.'&no_html=1&data=locations');
$dataurl = str_replace('&amp;','&',$dataurl);

$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<title>Usage Statistics Map</title>
<style type="text/css">
v\:* {
behavior:url(#default#VML);
}
</style>
<script src="https://maps.google.com/maps?file=api&amp;v=2&amp;key='.$key.'" type="text/javascript"> </script>
<script src="' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/js/Clusterer2.js" type="text/javascript"> </script>
<script type="text/javascript">
	//<![CDATA[
	function load() {
		if (GBrowserIsCompatible()) {
		// this line below as suggested by google documentation does not work!
			//var map = new GMap2(document.getElementById("div_map"));
			map = new GMap2(document.getElementById("div_map"));
			map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.addControl(new GOverviewMapControl());
		//map.setCenter(new GLatLng(40.4091, -86.8592), 4);
		//map.setCenter(new GLatLng(40.4091, -86.8592), 4, G_SATELLITE_MAP);
		map.setCenter(new GLatLng(40.4091, -86.8592), 6, G_HYBRID_MAP);

		clusterer = new Clusterer(map);

		// This code below was to get the co-ordinates of the center of the map. doesn\'t work. look into it later.
		GEvent.addListener(map, "moveend", function() {
			var center = map.getCenter();
			document.getElementById("message").innerHTML = center.toString();
		});

		getMarkers();

		GEvent.addListener(map, "click", function(overlay, point) {
			if (overlay) {     // marker clicked
				overlay.openInfoWindowHtml(overlay.infowindow);   // open InfoWindow
				} else if (point) {       // background clicked
			}
			});
		}
	}

	function getMarkers() {
	//var urlstr="' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/read.php";
	var urlstr="'.$dataurl.'";
	var icon="' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/marker_red.png";
		var request = GXmlHttp.create();
		request.open(\'GET\', urlstr , true); // request XML from PHP with AJAX call
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
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/11.png";
			}
			if ( usage > 2 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/10.png";
			}
			if ( usage > 10 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/9.png";
			}
			if ( usage > 50 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/8.png";
			}
			if ( usage > 100 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/7.png";
			}
			if ( usage > 250 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/6.png";
			}
			if ( usage > 500 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/5.png";
			}
			if ( usage > 1000 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/4.png";
			}
			if ( usage > 5000 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/3.png";
			}
			if ( usage > 10000 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/2.png";
			}
			if ( usage > 25000 ) {
				icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/1.png";
			}

			// icon.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
			// icon.shadow = "marker_shadow.png";
			icon.iconSize = new GSize(20,34);
			// icon.shadowSize = new GSize(22, 30);
			icon.iconAnchor = new GPoint(10,34);
			icon.infoWindowAnchor = new GPoint(5, 1);

			markers[i] = new GMarker(new GLatLng(locations[i].getAttribute("lat"),locations[i].getAttribute("lng")),icon);

			// Add attributes to the marker so we can poll them later.
			// When clicked, an overlay will have these properties.
			// markers[i].infowindow = locations[i].getAttribute("domain")+":&nbsp;"+locations[i].getAttribute("hits")+" simulation jobs";
			markers[i].infowindow = locations[i].getAttribute("domain");
			//markers[i].infowindow = "purdue.edu:<br>&nbsp;&nbsp;62 simulation users<br>&nbsp; 1,010 simulation jobs<br>&nbsp; 218 days+ of simulation walltime ";
			// Useful things to store on a marker (Not needed for this example, could be removed)
			// Tells you what index in the markers[] array an overlay is
			markers[i].markerindex = i;
			// Store the location_id of the location the marker represents.
			// Very useful to know the true id of a marker, you could then make
			// AJAX calls to the database to update the information if you had it\'s location_id
			// changed location_id to ip
			markers[i].db_id = locations[i].getAttribute("ip");
			map.addOverlay(markers[i]);
			//clusterer.SetIcon(icon);
			//clusterer.AddMarker(markers[i]);
			}
		}
		}
	   }
	   request.send(null);
	}
	//]]>
</script>
</head>
<body onload="load()" onunload="GUnload()">
<div id="div_map" style="width:1280px; height: 860px"> </div>
<table>
<tr>
<td>Usage:&nbsp;&nbsp;&nbsp;</td>
<td>&nbsp;&nbsp;&nbsp;<img src="1.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="2.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="3.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="4.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="5.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="6.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="7.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="8.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="9.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="10.png" width=12 height=20/>&nbsp;&nbsp;&nbsp;></td>
<td>&nbsp;&nbsp;&nbsp;<img src="11.png" width=12 height=20/></td>
</tr>
</table>
</body>
</html>';

