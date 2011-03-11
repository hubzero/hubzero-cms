<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_stations()
{
	$dd['title'] = "Stations";
	$dd['table'] = 'Stations';
	$dd['pk'] = 'Stations.StationID';

	$dd['cols']['Stations.StationID'] = array('label'=>'Station', 'raw'=>'UPPER(Stations.StationID)', 'filtered_view'=>array('view'=>'spreadsheet', 'data'=>'events','filter'=>array('Events.Station'=>'Stations.StationID', 'Events.Magnitude'=>'>=4|number'))); // added UPPER()
	$dd['cols']['Stations.StationName'] = array('label'=>'Station Name', 'width'=>'185');
	$dd['cols']['Stations.Elevation'] = array('label'=>'Elevation<br />[m]');
	$dd['cols']['Stations.Latitude'] = array('label'=>'Latitude', 'data_type'=>'float');
	$dd['cols']['Stations.Logitude'] = array('label'=>'Longitude', 'data_type'=>'float');
	$dd['cols']['Stations.Thumbnail'] = array('label'=>'Station Image', 'type'=>'image');
	$dd['cols']['Stations.StationDescription'] = array('label'=>'Description', 'width'=>'200', 'truncate'=>'truncate');
	$dd['cols']['Stations.FacilityWebSite'] = array('label'=>'Facility<br />WebSite', 'type'=>'link', 'link_name', 'host');
	$dd['cols']['Stations.SurfaceLayout'] = array('label'=>'Surface<br />Layout', 'type'=>'image', 'resized'=>'resized');
	$dd['cols']['Stations.DownholeArray'] = array('label'=>'Downhole<br />Array', 'type'=>'image', 'resized'=>'resized');
	$dd['cols']['Stations.VelocityLogs'] = array('label'=>'Velocity<br />Logs', 'type'=>'image', 'resized'=>'resized');
	$dd['cols']['Stations.OnDate'] = array('label'=>'On Date', 'raw'=>"REPLACE(Stations.OnDate, 'T00:00:00.000Z', '')");

	$dd['show_maps'] = true;
	$dd['maps'][] = array('title'=>'Stations.StationName', 'lat'=>'Stations.Latitude', 'lng'=>'Stations.Logitude');

	return $dd;
}
?>
