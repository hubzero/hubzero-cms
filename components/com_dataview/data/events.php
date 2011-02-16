<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_events()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	$dd['serverside'] = true;
	$dd['title'] = "Events";
//	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	$dd['table'] = 'Events';

	$dd['cols']['Events.Event_ID'] = array('label'=>'Event ID', 'more_info'=>'events|Events.Event_ID', 'compare'=>'Compare selected Events');
	$dd['cols']['Events.Station'] = array('label'=>'Station', 'align'=>'center','data_type'=>'string', 'more_info'=>'stations|Events.Station');
	$dd['cols']['Events.Magnitude'] = array('label'=>'Magnitude', 'align'=>'center', 'data_type'=>'float');
	$dd['cols']['Events.Latitude'] = array('label'=>'Latitude', 'align'=>'right','data_type'=>'float');
	$dd['cols']['Events.Longitude'] = array('label'=>'Longitude', 'align'=>'right','data_type'=>'float');
	$dd['cols']['Events.Depth'] = array('label'=>'Depth', 'align'=>'center','data_type'=>'float');
	$dd['cols']['Events.Time'] = array('label'=>'Time', 'align'=>'center');
	$dd['cols']['Events.Azimuth'] = array('label'=>'Azimuth', 'align'=>'center','data_type'=>'float');
	$dd['cols']['Events.Distance'] = array('label'=>'Distance', 'align'=>'center','data_type'=>'float');

	$dd['show_maps'] = true;
	$dd['maps'][] = array('title'=>'Events.Event_ID', 'lat'=>'Events.Latitude', 'lng'=>'Events.Longitude');

	if ($id) {
		$dd['where'][] = array('field'=>'Events.Event_ID', 'value'=>$id);
		$dd['single'] = true;
	}
	
	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
