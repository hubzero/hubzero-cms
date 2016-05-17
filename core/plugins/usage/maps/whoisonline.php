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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<script type="text/javascript" src="https://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $key; ?>"></script>
	<script type="text/javascript" src="<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/js/Clusterer2.js"></script>
	<script type="text/javascript">
	function load()
	{
		if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById('div_map'));
			map.addControl(new GLargeMapControl());
			map.addControl(new GMapTypeControl());
			map.addControl(new GOverviewMapControl());
			// map.setCenter(new GLatLng(".$lat.",".$lng."), ".$zoom.", G_PHYSICAL_MAP);
			// map.setCenter(new GLatLng(".$lat.",".$lng."), ".$zoom.", G_SATELLITE_MAP);
			map.setCenter(new GLatLng(<?php echo $lat; ?>,<?php echo $long; ?>), <?php echo $zoom; ?>, G_HYBRID_MAP);
			GEvent.addListener(map, 'click', function(overlay, point) {
				if (overlay) {     // marker clicked
					overlay.openInfoWindowHtml(overlay.infowindow);   // open InfoWindow
				} else if (point) {

				} // background clicked
			});
			getMarkers();
		}
	}

	function getMarkers()
	{
		var urlstr='<?php echo $mappath; ?>/whoisonline.xml';
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
						var bot = locations[i].getAttribute('bot');
						var icon = new GIcon();
						if (bot == '1') {
							icon.image = '<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/images/markerB.png';
						} else {
							icon.image = '<?php echo rtrim(Request::base(), '/'); ?>/core/plugins/usage/maps/images/markerU.png';
						}
						icon.iconSize = new GSize(18, 30);
						icon.iconAnchor = new GPoint(9, 30);
						icon.infoWindowAnchor = new GPoint(9, 15);
						markers[i] = new GMarker(new GLatLng(locations[i].getAttribute('lat'),locations[i].getAttribute('lng')),icon);
						var lat = locations[i].getAttribute('lat');
						var lng = locations[i].getAttribute('lng');
						var info = locations[i].getAttribute('info');
						info = info.replace(/_br_/g,'<br/>');
						info = info.replace(/_hr_/g,'<hr/>');
						info = info.replace(/_b_/g,'<b>');
						info = info.replace(/_bb_/g,'</b>');
						markers[i].infowindow = info.replace(/_br_/g,'<br/>');
						map.addOverlay(markers[i]);
						if (lat==<?php echo $lat; ?> && lng==<?php echo $long; ?>) {
							GEvent.trigger(markers[i], 'click');
						}
					}
				}
			}
		}
		request.send(null);
	}
	</script>
 </head>
 <body onload="load()" onunload="GUnload()">
	<div id="div_map" style="width:100%; height:600px"></div>
 </body>
</html>

