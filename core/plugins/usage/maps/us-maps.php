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

$period = Request::getString('period', '1999-12');

$date = $period;

$dataurl = Route::url('index.php?option='.$option.'&task='.$task.'&type='.$type.'&no_html=1&data=markers&local=us');
$dataurl = str_replace('&amp;', '&', $dataurl);

$html = "<!DOCTYPE html '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<style type='text/css'>
.style1 {background-color:#ffffff;font-size:2.5em;font-weight:bold;padding-left:3px;padding-right:3px;border:2px #000000 solid;}
.style2 {background-color:#ffffff;}
</style>
<meta http-equiv='content-type' content='text/html; charset=utf-8'/>
<title>User Animation</title>
<script type='text/javascript' src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$key."'></script>
<script type='text/javascript' src='" . rtrim(Request::base(), '/') . "/core/plugins/usage/maps/assets/js/elabel.js'></script>
<script type='text/javascript'>

	function initialize() {
		var dt = '".$date."';
		var plotdt = '".substr($date, 0, 7)."';
		if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById('map_canvas'));
			// map.addControl(new GLargeMapControl());
			map.addControl(new GSmallMapControl());
			// map.setCenter(new GLatLng(25.4091, -28.8592), 3, G_PHYSICAL_MAP);
			// map.setCenter(new GLatLng(20.0,11.0), 3, G_PHYSICAL_MAP);
			// map.setCenter(new GLatLng(35,-113), 5, G_HYBRID_MAP);
			map.setCenter(new GLatLng(35,-113), 5, G_SATELLITE_MAP);
			map.setCenter(new GLatLng(35,-113), 5, G_HYBRID_MAP);

			var icon1 = new GIcon();
			icon1.image = '" . rtrim(Request::base(), '/') . "/core/plugins/usage/maps/assets/img/org.png';
			icon1.iconSize = new GSize(40, 40);
			icon1.iconAnchor = new GPoint(20, 20);
			marker1 = new GMarker(new GLatLng('40.4427','-86.9237'),icon1);
			map.addOverlay(marker1);
			getMarkers(dt);
			var label = new ELabel(new GLatLng(-52.7,11.0),'".substr($date, 0, 7)."','style1');
			map.addOverlay(label);

			// plot overlay
			//var plt = 'plots/'+plotdt+'-14-u1.gif';
			//var boundaries = new GLatLngBounds(new GLatLng(-52.7,-168.1), new GLatLng(-3.02,-90.96));
			//var oldmap = new GGroundOverlay(plt, boundaries);
			//map.addOverlay(oldmap);

			// var polyline = new GPolyline([new GLatLng(-60.0, -110),new GLatLng(-10, -110)], '#0000ff', 320);
			// map.addOverlay(polyline);
			// var polyline = new GPolyline([new GLatLng(-40.0, -160),new GLatLng(-40.0, -10)], '#ff0000', 248);
			// map.addOverlay(polyline);
		}
	}


	function getMarkers(dt) {
		//var urlstr='/core/components/".$option."/maps/read_us_location.php?period='+dt;
		var urlstr='".$dataurl."&period='+dt;
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
						icon.image = '/core/components/".$option."/maps/images/1.png';
						icon.iconSize = new GSize(20, 34);
						icon.iconAnchor = new GPoint(10, 34);
						markers[i] = new GMarker(new GLatLng(locations[i].getAttribute('lat'),locations[i].getAttribute('lng')),icon);
						map.addOverlay(markers[i]);
					}
				}
			}
		}
		request.send(null);
	}
</script>

</head>
<body onload='initialize()' onunload='GUnload()'>
	<div id='map_canvas' style='width: 2200px; height: 1010px'></div>
</body>
</html>";
