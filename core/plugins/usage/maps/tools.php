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

$id = Request::getVar('id','');

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
	<script type='text/javascript' src='/components/".$option."/maps/js/Clusterer2.js'> </script>
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
						icon.image = '/components/".$option."/maps/images/1.png';
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
 </head>
 <body onload='load()' onunload='GUnload()'>
	<div id='div_map' style='width:1024px; height:600px'> </div>
 </body>
</html>";
}

