<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_sitechans()
{
	$dd['title'] = "Site Channels";
	$dd['table'] = 'sitechans';
	$dd['pk'] = 'sitechans.id';
	
	$dd['cols']['sitechans.id'] = array('label'=>'id', 'data_type'=>'int', 'more_info'=>'sitechans|sitechans.id', 'compare'=>'compare','desc'=>'Select two or more items to compare side-by-side', 'width'=>'70');
	$dd['cols']['sitechans.sta'] = array('label'=>'Station', 'more_info'=>'stations|sitechans.sta');
	$dd['cols']['sitechans.chan'] = array('label'=>'Channel');
	$dd['cols']['sitechans.loc'] = array('hide'=>'hide');
	$dd['cols']['sitechans.fchan'] = array('hide'=>'hide');
	$dd['cols']['sitechans.descrip'] = array('label'=>'Sensor & Datalogger<br />Serial Numbers');
	$dd['cols']['sitechans.edepth'] = array('label'=>'Depth');
	$dd['cols']['sitechans.ondate'] = array('label'=>'On Date', 'raw'=>"REPLACE(sitechans.ondate, ' 00:00:00', '')");

	$dd['order_by'] = array('sitechans.sta', 'sitechans.loc', 'sitechans.fchan');

	return $dd;
}
?>
