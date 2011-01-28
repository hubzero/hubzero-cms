<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_waveforms()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	$dd['title'] = "Waveforms";
	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	$dd['table'] = 'waveforms';

	$dd['cols']['waveforms.id'] = array('label'=>'id', 'data_type'=>'int', 'more_info'=>'waveforms|waveforms.id', 'compare'=>'compare','desc'=>'Select two or more items to compare side-by-side', 'width'=>'60');
	$dd['cols']['waveforms.chan'] = array('label'=>'Chan');
	$dd['cols']['waveforms.sta'] = array('label'=>'Station', 'more_info'=>'stations|waveforms.sta');
	$dd['cols']['waveforms.val1'] = array('label'=>'Val1', 'data_type'=>'float');
	$dd['cols']['waveforms.units1'] = array('label'=>'Units1');
	$dd['cols']['waveforms.val2'] = array('label'=>'Val2', 'data_type'=>'float');
	$dd['cols']['waveforms.units2'] = array('label'=>'Units2');
	$dd['cols']['waveforms.filter'] = array('label'=>'Filter', 'width'=>'200');
	$dd['cols']['waveforms.twin'] = array('label'=>'Twin');
	$dd['cols']['waveforms.auth'] = array('label'=>'Auth');
	$dd['cols']['waveforms.tmeas'] = array('label'=>'Tmeas', 'width'=>'130');
	$dd['cols']['waveforms.time'] = array('label'=>'Time', 'width'=>'130');
	$dd['cols']['waveforms.endtime'] = array('label'=>'Endtime', 'width'=>'130');
	$dd['cols']['waveforms.arid'] = array('label'=>'Arid');
	$dd['cols']['waveforms.meastype'] = array('label'=>'Meastype');

	if ($id) {
		$dd['where'][] = array('field'=>'waveforms.id', 'value'=>$id);
		$dd['single'] = true;
	}
	
	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
