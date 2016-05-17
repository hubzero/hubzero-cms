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

$dataurl = Route::url('index.php?option='.$option.'&task='.$task.'&type='.$type.'&no_html=1&data=markers');
$dataurl = str_replace('&amp;','&',$dataurl);

$html = '<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<style type="text/css">
	v\:* {
		behavior:url(#default#VML);
	}
	</style>

	<script type="text/javascript" src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$key.'"></script>
	<script type="text/javascript" src="' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/js/Clusterer2.js"></script>
	<script type="text/javascript">
	function load()
	{
		if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById("div_map"));
			map.addControl(new GLargeMapControl());
			map.setCenter(new GLatLng(25.4091, -28.8592), 3, G_PHYSICAL_MAP);
			getMarkers();
		}
	}

	function getMarkers()
	{
		//var urlstr="/components/'.$option.'/maps/read_location.php";
		var urlstr="'.$dataurl.'";
		var icon="/components/'.$option.'/maps/images/marker_red.png";
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
						icon.image = "' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/images/markerU.png";

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
						'. get_markers() .'
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
	<div id="div_map" style="width:1200px; height:600px"></div>
 </body>
</html>';

