<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_sites()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	$dd['title'] = "Sites";
	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	$dd['table'] = 'sites';

	$dd['cols']['sites.id'] = array('label'=>'id', 'data_type'=>'int', 'more_info'=>'sites|sites.id', 'compare'=>'compare','desc'=>'Select two or more items to compare side-by-side');
	$dd['cols']['sites.staname'] = array('label'=>'Station Name', 'width'=>'220', 'more_info'=>'stations|sites.sta');
	$dd['cols']['sites.ondate'] = array('label'=>'On Date', 'align'=>'center');
	$dd['cols']['sites.offdate'] = array('label'=>'Off Date', 'align'=>'center');
	$dd['cols']['sites.lat'] = array('label'=>'Latitude', 'align'=>'right', 'data_type'=>'float');
	$dd['cols']['sites.lon'] = array('label'=>'Longitude', 'align'=>'right', 'data_type'=>'float');
	$dd['cols']['sites.elev'] = array('label'=>'Elevation', 'align'=>'right', 'data_type'=>'float');
	$dd['cols']['sites.sta'] = array('label'=>'Station', 'more_info'=>'stations|sites.sta');

	$dd['show_maps'] = true;
	$dd['maps'][] = array('title'=>'sites.staname', 'lat'=>'sites.lat', 'lng'=>'sites.lon');

	if ($id) {
		$dd['where'][] = array('field'=>'sites.id', 'value'=>$id);
		$dd['single'] = true;
	}
	
	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
