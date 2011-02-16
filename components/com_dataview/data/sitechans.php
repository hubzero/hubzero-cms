<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_sitechans()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	$dd['title'] = "Site Channels";
//	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	$dd['table'] = 'sitechans';
	
	$dd['cols']['sitechans.id'] = array('label'=>'id', 'data_type'=>'int', 'more_info'=>'sitechans|sitechans.id', 'compare'=>'compare','desc'=>'Select two or more items to compare side-by-side', 'width'=>'70');
	$dd['cols']['sitechans.sta'] = array('label'=>'Station', 'more_info'=>'stations|sitechans.sta');
	$dd['cols']['sitechans.chan'] = array('label'=>'Channel');
	$dd['cols']['sitechans.loc'] = array('hide'=>'hide');
	$dd['cols']['sitechans.fchan'] = array('hide'=>'hide');
	$dd['cols']['sitechans.descrip'] = array('label'=>'Sensor & Datalogger<br />Serial Numbers');
	$dd['cols']['sitechans.edepth'] = array('label'=>'Depth');
	$dd['cols']['sitechans.ondate'] = array('label'=>'On Date', 'raw'=>"REPLACE(sitechans.ondate, ' 00:00:00', '')");

	$dd['order_by'] = array('sitechans.sta', 'sitechans.loc', 'sitechans.fchan');

	if ($id) {
		$dd['where'][] = array('field'=>'sitechans.id', 'value'=>$id);
		$dd['single'] = true;
	}
	
	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
