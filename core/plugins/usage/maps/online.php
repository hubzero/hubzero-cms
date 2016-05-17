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

$lat = 20;
$long = 0;
$zoom = 2;

$dataurl = Route::url('index.php?option='.$option.'&task='.$task.'&type='.$type.'&no_html=1&data=online');
$dataurl = str_replace('&amp;','&',$dataurl);

$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key='. $key .'&sensor=false"> </script>
	<script type="text/javascript" src="' . rtrim(Request::base(), '/') . '/core/plugins/usage/maps/js/util.js"> </script>

	<script type="text/javascript">

  		var infowindow;
  		var map;

  		function initialize() {
    		var myLatlng = new google.maps.LatLng('. $lat .','. $long .');
    		var myOptions = {
      		zoom: '. $zoom .',
      		center: myLatlng,
      		mapTypeId: google.maps.MapTypeId.ROADMAP
    		}
    		map = new google.maps.Map(document.getElementById("div_map"), myOptions);
			'."\n";
			if ($mappath) {
				$html .= 'var urlstr=\''.$mappath.'/whoisonline.xml\';'."\n";
			} else {
				$html .= 'var urlstr="'.$dataurl.'";'."\n";
			}

			$html .= 'downloadUrl(urlstr, function(data) {
      			var markers = data.documentElement.getElementsByTagName("marker");
      			for (var i = 0; i < markers.length; i++) {
        			var latlng = new google.maps.LatLng(parseFloat(markers[i].getAttribute("lat")), parseFloat(markers[i].getAttribute("lng")));
        			var marker = createMarker(markers[i].getAttribute("info"), latlng);
       			}
     		});
  		}

		function createMarker(info, latlng) {
     		info = info.replace(/_br_/g,\'<br/>\');
     		info = info.replace(/_hr_/g,\'<hr/>\');
     		info = info.replace(/_b_/g,\'<b>\');
     		info = info.replace(/_bb_/g,\'</b>\');
    		var marker = new google.maps.Marker({position: latlng, map: map});
    		google.maps.event.addListener(marker, "click", function() {
      			if (infowindow) infowindow.close();
     			infowindow = new google.maps.InfoWindow({content: info});
     			infowindow.open(map, marker);
    		});
    		return marker;
  		}

		</script>
 </head>
 <body onload="initialize()">
	<div id="div_map" style="width:100%; height:600px"></div>
 </body>
</html>';
