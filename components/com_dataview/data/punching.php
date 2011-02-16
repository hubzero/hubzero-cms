<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_punching()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	$dd['title'] = "Punching Shear Databank";
//	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	$dd['table'] = 'Punching';

	$dd['cols']['Punching.ID'] = array('label'=>'ID', 'data_type'=>'int');
	$dd['cols']['Punching.Authors'] = array('label'=>'Authors', 'width'=>'150');
	$dd['cols']['Punching.Year_Of_Pub'] = array('label'=>'Year of<br />Publication', 'align'=>'right');
	$dd['cols']['Punching.Reference_Link'] = array('label'=>'Reference Link', 'type'=>'link');
	$dd['cols']['Punching.Specimen'] = array('label'=>'Specimen');
	$dd['cols']['Punching.Setup'] = array('label'=>'Setup', 'type'=>'image');
	$dd['cols']['Punching.Load_Deflection_Curve'] = array('label'=>'Load-Deflection Curve', 'type'=>'image');
	$dd['cols']['Punching.Crack_Map'] = array('label'=>'Crack Map', 'type'=>'image');
	$dd['cols']['Punching.Age'] = array('label'=>'Age<br />( Days )', 'align'=>'right');
	$dd['cols']['Punching.Support_Conditions'] = array('label'=>'Support Conditions');
	$dd['cols']['Punching.Slab_Restraint'] = array('label'=>'Slab Restraint');
	$dd['cols']['Punching.Slab_Shape_In_Plan'] = array('label'=>'Slab Shape in Plan');
	$dd['cols']['Punching.Slab_Width'] = array('label'=>'Slab Width<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Slab_Diameter'] = array('label'=>'Slab Diameter<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Sp_Ld_Array_Shape'] = array('label'=>'Support/Loading-Array Shape');
	$dd['cols']['Punching.Sp_Ld_Array_Dimension'] = array('label'=>'Support/Loading-Array Side Dimension<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Sp_Ld_Array_Dimeter'] = array('label'=>'Support/Loading-Array Diameter<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Slab_Thickness'] = array('label'=>'Slab Thickness<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Shape_of_Column'] = array('label'=>'Shape of Column or Loading Plate');
	$dd['cols']['Punching.Side_Dimension_Of_Column'] = array('label'=>'Side Dimension of Column or Loading Plate<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Dimeter_Of_Column'] = array('label'=>'Diameter of Column or Loading Plate<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Conc_Comp_Str_Type_Dimension'] = array('label'=>'Concrete Compressive Strength / Type and Dimensions of Specimen');
	$dd['cols']['Punching.Conc_Comp_Str'] = array('label'=>'Concrete Compressive Strength<br />( MPa )', 'align'=>'right');
	$dd['cols']['Punching.Conc_Comp_Str_At_28'] = array('label'=>'Concrete Compressive Strength at 28 days<br />( MPa )', 'align'=>'right');
	$dd['cols']['Punching.Conc_Split_Str_At_Time'] = array('label'=>'Concrete Splitting Strength at Time of Test<br />( MPa )', 'align'=>'right');
	$dd['cols']['Punching.Modulus_Of_Rupture_At_Time'] = array('label'=>'Modulus of Rupture at Time of Test<br />( MPa )', 'align'=>'right');
	$dd['cols']['Punching.Uniaxial_Tensile_Str_At_Time'] = array('label'=>'Uniaxial Tensile Strength at Time of Testing<br />( MPa )', 'align'=>'right');
	$dd['cols']['Punching.Modulus_Of_Elasticity_At_Time'] = array('label'=>'Modulus of Elasticity at Time of Test<br />( MPa )', 'align'=>'right');
	$dd['cols']['Punching.Aggregate_Size'] = array('label'=>'Aggregate Size<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Reinforcement_Yield_Str'] = array('label'=>'Reinforcement Yield Strength<br />( MPa )', 'align'=>'right');
	$dd['cols']['Punching.Reinforcement_Tensile_Str'] = array('label'=>'Reinforcement Tensile Strength<br />( MPa )', 'align'=>'right');
	$dd['cols']['Punching.Reinforcement_Modulus_Of_Elasticity'] = array('label'=>'Reinforcement Modulus of Elasticity<br />( GPa )', 'align'=>'right');
	$dd['cols']['Punching.Type_Of_Slab_Reinf'] = array('label'=>'Type of Slab Reinf.');
	$dd['cols']['Punching.Bar_Surface'] = array('label'=>'Bar Surface');
	$dd['cols']['Punching.Bar_Ends'] = array('label'=>'Bar Ends');
	$dd['cols']['Punching.Effective_Depth_X_Dir'] = array('label'=>'Effective Depth, x-direction<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Effective_Depth_Y_Dir'] = array('label'=>'Effective Depth, y-direction<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Bar_Diameter_X_Dir'] = array('label'=>'Bar Diameter, x-direction<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Bar_Diameter_Y_Dir'] = array('label'=>'Bar Diameter, y-direction<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Reinf_Distribution'] = array('label'=>'Reinf. Distribution');
	$dd['cols']['Punching.Reinf_Spaxing_X_Dir'] = array('label'=>'Reinf. Spaxing, x-direction<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Reinf_Spaxing_Y_Dir'] = array('label'=>'Reinf. Spaxing, y-direction<br />( mm )', 'align'=>'right');
	$dd['cols']['Punching.Mean_Reinf_Ratio'] = array('label'=>'Mean Reinf. Ratio', 'align'=>'right');
	$dd['cols']['Punching.Max_Load'] = array('label'=>'Max Load<br />( kN )', 'align'=>'right');
	$dd['cols']['Punching.Reported_Failure_Mode'] = array('label'=>'Reported Failure Mode');
	$dd['cols']['Punching.Observations'] = array('label'=>'Observations', 'width'=>'100', 'truncate'=>'truncate');

	if ($id) {
		$dd['where'][] = array('field'=>'Punching.ID', 'value'=>$id);
		$dd['single'] = true;
	}
	
	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
