<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $key; ?>"></script>
	<script type="text/javascript" src="/components/<?php echo $option; ?>/maps/js/Clusterer2.js"></script>
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
				if (overlay){     // marker clicked
					overlay.openInfoWindowHtml(overlay.infowindow);   // open InfoWindow
				} else if (point) {
					
				}      // background clicked
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
							icon.image = '/components/<?php echo $option; ?>/maps/images/markerB.png';
						} else {
							icon.image = '/components/<?php echo $option; ?>/maps/images/markerU.png';
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
						if (lat==<?php echo $lat; ?> && lng==<?php echo $long; ?>){
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
