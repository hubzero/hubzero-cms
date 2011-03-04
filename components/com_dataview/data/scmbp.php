<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_scmbp()
{
	$dd['title'] = "Structural Control and Monitoring Benchmark Problems";
	$dd['table'] = 'Benchmark';
	$dd['pk'] = 'Benchmark.ID';

	$dd['cols']['Benchmark.ID'] = array('label'=>'ID', 'data_type'=>'int');
	$dd['cols']['Benchmark.Long_Title'] = array('hide'=>'hide');
	$dd['cols']['Benchmark.Title'] = array('label'=>'Title', 'width'=>'150', 'abbr'=>'Benchmark.Long_Title');
	$dd['cols']['Benchmark.Long_Authors'] = array('hide'=>'hide');
	$dd['cols']['Benchmark.Authors'] = array('label'=>'Authors', 'width'=>'150','abbr'=>'Benchmark.Long_Authors');
	$dd['cols']['Benchmark.Citation'] = array('label'=>'Citation', 'width'=>'200', 'truncate'=>'truncate');
	$dd['cols']['Benchmark.Specimen_Name'] = array('label'=>'Specimen Name', 'hide'=>'hide');
	$dd['cols']['Benchmark.Specimen_Link'] = array('label'=>'Specimen', 'type'=>'image');
	$dd['cols']['Benchmark.Video_Name'] = array('hide'=>'hide');
	$dd['cols']['Benchmark.Video_Link'] = array('label'=>'Video', 'type'=>'link', 'link_label'=>'Benchmark.Video_Name');
	$dd['cols']['Benchmark.Problem_Statement'] = array('hide'=>'hide');
	$dd['cols']['Benchmark.Problem_Statement_Link'] = array('label'=>'Problem Statement', 'type'=>'link', 'link_label'=>'Benchmark.Problem_Statement');
	$dd['cols']['Benchmark.Matlab_Zip'] = array('hide'=>'hide');
	$dd['cols']['Benchmark.Matlab_Zip_Link'] = array('label'=>'Matlab Zip', 'type'=>'link', 'link_label'=>'Benchmark.Matlab_Zip');

	return $dd;
}
?>
