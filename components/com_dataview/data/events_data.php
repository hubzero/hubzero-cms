<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_events_data()
{
	$dd['title'] = "Events Data";
	$dd['table'] = 'event_data';
	$dd['pk'] = 'event_data.event_id';

	$dd['cols']['event_data.event_id'] = array('label'=>'Event ID');
	$dd['cols']['event_data.station_id'] = array('label'=>'Station');
	$dd['cols']['event_data.channel_id'] = array('label'=>'Channel');
	$dd['cols']['event_data.file_path'] = array('label'=>'Data File', 'width'=>'200', 'type'=>'tool', 'name'=>'inDEED', 'link_format'=>'https://nees.org/tools/indeed/invoke?list={p}', 'param'=>'event_data.file_path', 'link_label'=>'event_data.channel_id', 'dl'=>'event_data.file_path');

	return $dd;
}
?>
