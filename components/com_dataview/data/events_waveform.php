<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_events_waveform()
{
	$dd['serverside'] = true;
	$dd['title'] = "Events with Waveform Data";
	$dd['table'] = 'Events';
	$dd['pk'] = 'Events.Event_ID';
	
	$dd['join'][] = array('table'=>"event_data", 'type'=>'JOIN', 'fields'=>array('Events.Event_ID'=>'event_data.event_id', 'Events.Station'=>'event_data.station_id'));
	
	$dd['cols']['Events.Event_ID'] = array('label'=>'Event ID', 'more_info'=>'events|Events.Event_ID', 'compare'=>'Compare selected Events');
	$dd['cols']['Events.Station'] = array('label'=>'Station', 'align'=>'center','data_type'=>'string', 'more_info'=>'stations|Events.Station');
	$dd['cols']['event_data.event_id'] = array('label'=>'Data', 'desc'=>'TODO:Add a better description, Get channel list...Launch inDEED', 'filtered_view'=>array('view'=>'spreadsheet', 'data'=>'events_data','filter'=>array('event_data.event_id'=>'Events.Event_ID', 'event_data.station_id'=>'Events.Station')), 'raw'=>'IF(event_data.event_id IS NOT NULL, "View Data", NULL)');
	$dd['cols']['Events.Magnitude'] = array('label'=>'Magnitude', 'align'=>'center', 'data_type'=>'float');
	$dd['cols']['Events.Latitude'] = array('label'=>'Latitude', 'align'=>'right','data_type'=>'float');
	$dd['cols']['Events.Longitude'] = array('label'=>'Longitude', 'align'=>'right','data_type'=>'float');
	$dd['cols']['Events.Depth'] = array('label'=>'Depth', 'align'=>'center','data_type'=>'float');
	$dd['cols']['Events.Time'] = array('label'=>'Time', 'align'=>'center');
	$dd['cols']['Events.Azimuth'] = array('label'=>'Azimuth', 'align'=>'center','data_type'=>'float');
	$dd['cols']['Events.Distance'] = array('label'=>'Distance', 'align'=>'center','data_type'=>'float');

	$dd['group_by'] = 'Events.Event_ID, Events.Station';
	
	$dd['show_maps'] = true;
	$dd['maps'][] = array('title'=>'Events.Event_ID', 'lat'=>'Events.Latitude', 'lng'=>'Events.Longitude');

	return $dd;
}
?>
