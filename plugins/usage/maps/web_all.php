<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$dataurl = JRoute::_('index.php?option='.$option.'&task='.$task.'&type='.$type.'&no_html=1&data=locations');
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
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$key.'" type="text/javascript"> </script>
<script src="/components/'.$option.'/maps/js/Clusterer2.js" type="text/javascript"> </script>
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
	    map.setCenter(new GLatLng(40.4091, -86.8592), 4, G_SATELLITE_MAP);

	    clusterer = new Clusterer(map);

	    // This code below was to get the co-ordinates of the center of the map. doesn\'t work. look into it later.
	    GEvent.addListener(map, "moveend", function() {
  	        var center = map.getCenter();
  	        document.getElementById("message").innerHTML = center.toString();
	    });

	    getMarkers();

	    GEvent.addListener(map, "click", function(overlay, point) {
	        if (overlay){     // marker clicked
	            overlay.openInfoWindowHtml(overlay.infowindow);   // open InfoWindow
            	} else if (point) {       // background clicked
	    	}
            });
        }
    }

	function getMarkers()
	{
		//var urlstr="/components/'.$option.'/maps/read.php";
		var urlstr="'.$dataurl.'";
		var icon="/components/'.$option.'/maps/images/marker_red.png";
        var request = GXmlHttp.create();
        request.open(\'GET\', urlstr , true); // request XML from PHP with AJAX call
        request.onreadystatechange = function () {
       	    if (request.readyState == 4) {
                var xmlDoc = request.responseXML;
                locations = xmlDoc.documentElement.getElementsByTagName("location");
                markers = [];
                if (locations.length){
		    for (var i = 0; i < locations.length; i++) { // cycle thru locations

			var usage = locations[i].getAttribute("hits");
			var icon = new GIcon();

			icon.image = "/components/'.$option.'/maps/images/marker.png";

			// icon.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
			// icon.shadow = "marker_shadow.png";
			// icon.iconSize = new GSize(18, 30);
			icon.iconSize = new GSize(60,100);
			// icon.shadowSize = new GSize(22, 30);
			icon.iconAnchor = new GPoint(30,100);
			//icon.infoWindowAnchor = new GPoint(5, 1);

            markers[i] = new GMarker(new GLatLng(locations[i].getAttribute("lat"),locations[i].getAttribute("lng")),icon);

            // Add attributes to the marker so we can poll them later.
            // When clicked, an overlay will have these properties.
            // markers[i].infowindow = locations[i].getAttribute("domain")+":&nbsp;"+locations[i].getAttribute("hits")+" simulation jobs";
            //markers[i].infowindow = locations[i].getAttribute("domain");
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
</body>
</html>';

