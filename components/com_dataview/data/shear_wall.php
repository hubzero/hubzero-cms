<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_shear_wall()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	//Data definition
	$dd['title'] = "Shear Wall Database";
	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	$dd['table'] = 'ShearWallDat';

	$dd['cols']['ShearWallDat.ID'] = array('label'=>'ID', 'data_type'=>'int');
	$dd['cols']['ShearWallDat.Author'] = array('label'=>'Author', 'width'=>'130');
	$dd['cols']['ShearWallDat.ReferenceName'] = array('label'=>'Reference Name', 'hide'=>'hide');
	$dd['cols']['ShearWallDat.ReferenceLink'] = array('label'=>'Reference', 'type'=>'link', 'link_label'=>'ShearWallDat.ReferenceName', 'width'=>'130');
	$dd['cols']['ShearWallDat.Citation'] = array('label'=>'Citation', 'width'=>'200', 'truncate'=>'truncate');
	$dd['cols']['ShearWallDat.Specimen_Name'] = array('label'=>'Specimen Name', 'hide'=>'hide');
	$dd['cols']['ShearWallDat.SpecimenLink'] = array('label'=>'Specimen', 'type'=>'image');
	$dd['cols']['ShearWallDat.Plot_Image_Link'] = array('label'=>'Plot', 'type'=>'image');
	$dd['cols']['ShearWallDat.Data_File_Path'] = array('hide'=>'hide');
//	$dd['cols']['ShearWallDat.Data_File'] = array('label'=>'Data File', 'width'=>'200', 'type'=>'tool', 'name'=>'inDEED', 'link_format'=>'https://nees.org/tools/indeed/invoke?list={p}', 'param'=>'ShearWallDat.Data_File_Path', 'link_label'=>'ShearWallDat.Data_File', 'dl'=>'ShearWallDat.Data_File_Path');
	$dd['cols']['ShearWallDat.V_max'] = array('label'=>'V.max<br />( kN )', 'align'=>'right');
	$dd['cols']['ShearWallDat.P'] = array('label'=>'P<br />( kN )', 'desc'=>'Applied axial load', 'align'=>'right');
	$dd['cols']['ShearWallDat.P_f_cAg'] = array('label'=>"P/(f'c*Ag)", 'align'=>'right');
	$dd['cols']['ShearWallDat.F_c'] = array('label'=>"f'c", 'desc'=>'Concrete compressive strength', 'align'=>'right');
	$dd['cols']['ShearWallDat.hw'] = array('label'=>"hw<br />( mm )", 'desc'=>'Wall height', 'align'=>'right');
	$dd['cols']['ShearWallDat.lw'] = array('label'=>"lw<br />( mm )", 'desc'=>'Wall length', 'align'=>'right');
	$dd['cols']['ShearWallDat.tw'] = array('label'=>"tw<br />( mm )", 'desc'=>'Wall thickness', 'align'=>'right');
	$dd['cols']['ShearWallDat.cw'] = array('label'=>"cw<br />( mm )", 'desc'=>'Cover thickness in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.d_wl'] = array('label'=>"d.wl<br />( mm )", 'desc'=>'Diameter of vertical reinforcement in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.s_wl'] = array('label'=>"s.wl<br />( mm )", 'desc'=>'Spacing of vertical reinforcement in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.fy_wl'] = array('label'=>"fy.wl<br />( MPa )", 'desc'=>'Yield stress of vertical reinforcement in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.fu_wl'] = array('label'=>"fu.wl<br />( MPa )", 'desc'=>'Ultimate stress of vertical reinforcement in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.d_wv'] = array('label'=>"d.wv<br />( mm )", 'desc'=>'Diameter of transverse reinforcement in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.s_wv'] = array('label'=>"s.wv<br />( mm )", 'desc'=>'Spacing of transverse reinforcement in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.fy_wv'] = array('label'=>"fy.wv<br />( MPa )", 'desc'=>'Yield stress of transverse reinforcement in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.fu_wv'] = array('label'=>"fu.wv<br />( MPa )", 'desc'=>'Ultimate stress of transverse reinforcement in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.bb'] = array('label'=>"bb<br />( mm )", 'desc'=>'Width of boundary element', 'align'=>'right');
	$dd['cols']['ShearWallDat.hb'] = array('label'=>"hb<br />( mm )", 'desc'=>'Thickness of boundary element', 'align'=>'right');
	$dd['cols']['ShearWallDat.cb'] = array('label'=>"cb<br />( mm )", 'desc'=>'Cover thickness in boundary element', 'align'=>'right');
	$dd['cols']['ShearWallDat.d_bl'] = array('label'=>"d.bl<br />( mm )", 'desc'=>'Diameter of longitudinal reinforcement in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.s_bl'] = array('label'=>"s.bl<br />( mm )", 'desc'=>'Spacing of longitudinal reinforcement in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.fy_bl'] = array('label'=>"fy.bl<br />( MPa )", 'desc'=>'Yield stress of longitudinal reinforcement in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.fu_bl'] = array('label'=>"fu.bl<br />( MPa )", 'desc'=>'Ultimate stress of longitudinal reinforcement in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.d_bv'] = array('label'=>"d.bv<br />( mm )", 'desc'=>'Diameter of transverse reinforcement in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.s_bv'] = array('label'=>"s.bv<br />( mm )", 'desc'=>'Spacing of transverse reinforcement in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.fy_bv'] = array('label'=>"fy.bv<br />( MPa )", 'desc'=>'Yield stress of transverse reinforcement in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.fu_bv'] = array('label'=>"fu.bv<br />( MPa )", 'desc'=>'Ultimate stress of transverse reinforcement in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.rho_wl'] = array('label'=>"rho.wl", 'desc'=>'Vertical reinforcement ratio in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.rho_wh'] = array('label'=>"rho.wh", 'desc'=>'Horizontal reinforcement ratio in web', 'align'=>'right');
	$dd['cols']['ShearWallDat.rho_bl'] = array('label'=>"rho.bl", 'desc'=>'Longitudinal reinforcement ratio in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.rho_bv'] = array('label'=>"rho.bv", 'desc'=>'Transverse reinforcement ratio in Boundary Element', 'align'=>'right');
	$dd['cols']['ShearWallDat.delta_y'] = array('label'=>'delta.y');
	$dd['cols']['ShearWallDat.delta_y_hw'] = array('label'=>'delta.y/hw');
	$dd['cols']['ShearWallDat.delta_pk'] = array('label'=>'delta.pk');
	$dd['cols']['ShearWallDat.delta_pk_hw'] = array('label'=>'delta.pk/hw');
	$dd['cols']['ShearWallDat.delta_u'] = array('label'=>'delta.u');
	$dd['cols']['ShearWallDat.delta_u_hw'] = array('label'=>'delta.u/hw');
	$dd['cols']['ShearWallDat.Comment'] = array('label'=>'Comment', 'width'=>'200', 'truncate'=>'truncate');

	if ($id) {
		$dd['where'][] = array('field'=>'ShearWallDat.ID', 'value'=>$id);
		$dd['single'] = true;
	}
	
	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
