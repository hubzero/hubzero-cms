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

$id = JRequest::getVar('id','');

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
    function getMarkers(){
		var urlstr='".$mappath."/resource_maps/".$id.".xml';
        var request = GXmlHttp.create();
        request.open('GET', urlstr , true); // request XML from PHP with AJAX call
        request.onreadystatechange = function () {
       		if (request.readyState == 4) {
        		var xmlDoc = request.responseXML;
            	locations = xmlDoc.documentElement.getElementsByTagName('marker');
            	markers = [];
            	if (locations.length){
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
