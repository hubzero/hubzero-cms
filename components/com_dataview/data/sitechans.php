<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_sitechans()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	$dd['title'] = "Site Channels";
	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	$dd['table'] = 'sitechans';
	
	$dd['cols']['sitechans.id'] = array('label'=>'id', 'data_type'=>'int', 'more_info'=>'sitechans|sitechans.id', 'compare'=>'compare','desc'=>'Select two or more items to compare side-by-side', 'width'=>'70');
	$dd['cols']['sitechans.sta'] = array('label'=>'Station', 'more_info'=>'stations|sitechans.sta');
//	$dd['cols']['sitechans.fchanchanid'] = array('label'=>'Fchan ID', 'data_type'=>'int');
	$dd['cols']['sitechans.fchan'] = array('label'=>'Channel');
	$dd['cols']['sitechans.loc'] = array('label'=>'Location', 'data_type'=>'int');
//	$dd['cols']['sitechans.chan'] = array('label'=>'Chan');
	$dd['cols']['sitechans.vang'] = array('label'=>'V angle', 'data_type'=>'int');
	$dd['cols']['sitechans.hang'] = array('label'=>'H angle', 'data_type'=>'int');
	$dd['cols']['sitechans.ondate'] = array('label'=>'On Date', 'align'=>'left');
	$dd['cols']['sitechans.offdate'] = array('label'=>'Off Date', 'align'=>'left');
	$dd['cols']['sitechans.descrip'] = array('label'=>'Description');
	$dd['cols']['sitechans.edepth'] = array('label'=>'Edepth', 'data_type'=>'float');
	$dd['cols']['sitechans.loc'] = array('label'=>'Location', 'data_type'=>'int');
//	$dd['cols']['sitechans.ctype'] = array('label'=>'Ctype');

	if ($id) {
		$dd['where'][] = array('field'=>'sitechans.id', 'value'=>$id);
		$dd['single'] = true;
	}
	
	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
