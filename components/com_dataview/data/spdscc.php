<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_spdscc()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	//Data definition
	$dd['title'] = "Structural Performance Database for Spiral Concrete Columns";
	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	$dd['table'] = 'ColDat';

	$dd['cols']['ColDat.ID'] = array('label'=>'ID', 'data_type'=>'int');
	$dd['cols']['ColDat.Author'] = array('label'=>'Author', 'width'=>'130');
	$dd['cols']['ColDat.ReferenceName'] = array('label'=>'Reference Name', 'hide'=>'hide');
	$dd['cols']['ColDat.ReferenceLink'] = array('label'=>'Reference', 'type'=>'link', 'link_label'=>'ColDat.ReferenceName', 'width'=>'130');
	$dd['cols']['ColDat.Citation'] = array('label'=>'Citation', 'width'=>'200', 'truncate'=>'truncate');
	$dd['cols']['ColDat.Specimen_Name'] = array('label'=>'Specimen Name', 'hide'=>'hide');
	$dd['cols']['ColDat.SpecimenLink'] = array('label'=>'Specimen', 'type'=>'image');
	$dd['cols']['ColDat.Plot_Image_Link'] = array('label'=>'Plot', 'type'=>'image');
	$dd['cols']['ColDat.Data_File_Path'] = array('hide'=>'hide');
	$dd['cols']['ColDat.Data_File'] = array('label'=>'Data File', 'width'=>'200', 'type'=>'tool', 'name'=>'inDEED', 'link_format'=>'https://nees.org/tools/indeed/invoke?list={p}', 'param'=>'ColDat.Data_File_Path', 'link_label'=>'ColDat.Data_File', 'dl'=>'ColDat.Data_File_Path');
	$dd['cols']['ColDat.V_max'] = array('label'=>'V.max<br />( kN )', 'align'=>'right');
	$dd['cols']['ColDat.P'] = array('label'=>'P <br />( kN )', 'desc'=>'Applied axial load', 'align'=>'right');	
	$dd['cols']['ColDat.P_f_cAg'] = array('label'=>"P/(f'c*Ag)", 'align'=>'right');
	$dd['cols']['ColDat.F_c'] = array('label'=>"f'c<br />( Mpa )", 'desc'=>'Concrete compressive strength', 'align'=>'right');
	$dd['cols']['ColDat.D'] = array('label'=>'D<br />( mm )', 'align'=>'right');
	$dd['cols']['ColDat.L'] = array('label'=>'L<br />( mm )', 'align'=>'right');
	$dd['cols']['ColDat.Ag'] = array('label'=>'Ag<br />( mm&sup2; )', 'desc'=>'Gross cross-sectional area of specimen', 'align'=>'right');
	$dd['cols']['ColDat.cc'] = array('label'=>'cc<br />( mm )', 'align'=>'right');
	$dd['cols']['ColDat.Ac'] = array('label'=>'Ac<br />( mm&sup2; )', 'align'=>'right');
	$dd['cols']['ColDat.Nlong'] = array('label'=>'Nlong', 'align'=>'right');
	$dd['cols']['ColDat.db'] = array('label'=>'db<br />( mm )', 'align'=>'right');
	$dd['cols']['ColDat.Fy'] = array('label'=>'fy<br />( MPa )', 'align'=>'right');
	$dd['cols']['ColDat.rhog'] = array('label'=>'rhog<br />( % )', 'align'=>'right');
	$dd['cols']['ColDat.s'] = array('label'=>'s<br />( mm )', 'align'=>'right');
	$dd['cols']['ColDat.dsp'] = array('label'=>'dsp<br />( mm )', 'align'=>'right');
	$dd['cols']['ColDat.Fyv'] = array('label'=>'fyv<br />( MPa )', 'align'=>'right');
	$dd['cols']['ColDat.rhov'] = array('label'=>'rhov<br />( % )', 'align'=>'right');
	$dd['cols']['ColDat.Comment'] = array('label'=>'Comment', 'width'=>'200', 'truncate'=>'truncate');

	if ($id) {
		$dd['where'][] = array('field'=>'ColDat.ID', 'value'=>$id);
		$dd['single'] = true;
	}
	
	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
