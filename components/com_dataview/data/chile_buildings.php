<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_chile_buildings()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	$dd['title'] = "The Chile Earthquake Database: Buildings";
	$dd['table'] = 'chile_buildings';
	$dd['db'] = array('host'=> 'stage.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	
	$dd['cols']['chile_buildings.Number'] = array('label'=>'ID');
	$dd['cols']['chile_buildings.Building'] = array('label'=>'Building Name', 'desc'=>'Select two or more items to compare side-by-side', 'more_info'=>'chile_buildings|chile_buildings.Number', 'compare'=>'compare');
	$dd['cols']['"Damage"'] = array('label'=>'Damage', 'more_info_multi'=>'chile_damage|chile_buildings.Number');
//	$dd['cols']['"1985 Damage"'] = array('label'=>'Damage<br />1985', 'desc'=>'Click to view damage information for the building for 1985', 'more_info'=>'chile_damage_1985|chile_buildings.Number');
//	$dd['cols']['"2010 Damage"'] = array('label'=>'Damage<br />2010', 'desc'=>'Click to view damage information for the building for 1985', 'more_info'=>'chile_damage_2010|chile_buildings.Number');
	$dd['cols']['chile_buildings.Number_of_Structures'] = array('label'=>'Number of<br />Structures');
	$dd['cols']['chile_buildings.Address'] = array('label'=>'Address', 'width'=>'130');
	$dd['cols']['chile_buildings.Coordinates_Long'] = array('label'=>'Longitude', 'width'=>'90');
	$dd['cols']['chile_buildings.Coordinates_Lat'] = array('label'=>'Latitude', 'width'=>'90');
	$dd['cols']['chile_buildings.Number_of_Stories'] = array('label'=>'Number of<br />Stories');
	$dd['cols']['chile_buildings.Height'] = array('label'=>'Height<br />[m]');
	$dd['cols']['chile_buildings.Drawings'] = array('hide'=>'hide');
	$dd['cols']['chile_buildings.main_pic'] = array('label'=>'Drawings', 'type'=>'image', 'gallery'=>'chile_buildings.Drawings', 'resized'=>'resized');		// IF(me==sleeping) THEN pls update the fieldname and uncomment to use the mainpic when it's added
//	$dd['cols']['"/site/collections/haiti/mainpics/small/A001.JPG"'] = array('label'=>'Drawings', 'type'=>'image', 'gallery'=>'chile_buildings.Drawings');	// temp. gallery column
	$dd['cols']['chile_buildings.Date_of_Construction'] = array('label'=>'Date of<br />Construction');
	$dd['cols']['chile_buildings.Constructed_Area'] = array('label'=>'Constructed<br />Area [m²]');
	$dd['cols']['chile_buildings.Area_of_Columns'] = array('label'=>'Area of<br />Columns [m²]');
	$dd['cols']['chile_buildings.Area_of_Walls_x_Dir'] = array('label'=>'Area of Walls,<br />x-Direction [m²]');
	$dd['cols']['chile_buildings.Area_of_Walls_y_Dir'] = array('label'=>'Area of Walls,<br />y-Direction [m²]');
	$dd['cols']['chile_buildings.Reference_Level'] = array('label'=>'Reference<br />Level');
	$dd['cols']['chile_buildings.Floor_Area_above_Ref_Level'] = array('label'=>'Floor Area above<br />Reference Level [m²]');
	$dd['cols']['chile_buildings.Index_1'] = array('label'=>'Index 1');
	$dd['cols']['chile_buildings.Index_2'] = array('label'=>'Index 2<br />[kgf/cm²]');
	$dd['cols']['chile_buildings.Mean_Wall_Web_Ratio_Vertical'] = array('label'=>'Mean Wall Web<br />Ratio Vertical');
	$dd['cols']['chile_buildings.Mean_Wall_Web_Ratio_Horizontal'] = array('label'=>'Mean Wall Web<br />Ratio Horizontal');
	$dd['cols']['chile_buildings.Mean_Wall_Boundary_Ratio_Vertical'] = array('label'=>'Mean Wall Boundary<br />Ratio Vertical');
	$dd['cols']['chile_buildings.Mean_Wall_Boundary_Ratio_Horizontal'] = array('label'=>'Mean Wall Boundary<br />Ratio Horizontal');
	$dd['cols']['chile_buildings.Mean_Column_Ratio_Vertical'] = array('label'=>'Mean Column<br />Ratio Vertical');
	$dd['cols']['chile_buildings.Mean_Column_Ratio_Horizontal'] = array('label'=>'Mean Column<br />Ratio Horizontal');
	$dd['cols']['chile_buildings.Nominal_Concrete_Strength'] = array('label'=>'Nominal Concrete<br />Strength [kgf/cm²]');
	$dd['cols']['chile_buildings.Nominal_Steel_Yield_Stress'] = array('label'=>'Nominal Steel<br />Yield_Stress [kgf/cm²]');
	$dd['cols']['chile_buildings.Framing_System'] = array('label'=>'Framing<br />System');
	$dd['cols']['chile_buildings.Foundation_Type'] = array('label'=>'Foundation<br />Type', 'width'=>'200');
	$dd['cols']['chile_buildings.Partitions'] = array('label'=>'Partitions', 'width'=>'220');
	$dd['cols']['chile_buildings.Basements'] = array('label'=>'Basements');
	$dd['cols']['chile_buildings.Estimated_Period'] = array('label'=>'Estimated<br />Period [s]');
	$dd['cols']['chile_buildings.Measured_Period'] = array('label'=>'Measured<br />Period [s]');
	
	$dd['show_maps'] = true;
	$dd['maps'][] = array('title'=>'chile_buildings.Building', 'lat'=>'chile_buildings.Coordinates_Lat', 'lng'=>'chile_buildings.Coordinates_Long', 'cood_type'=>'dms');

	if ($id) {
		$dd['where'][] = array('field'=>'chile_buildings.Number', 'value'=>$id);
		$dd['single'] = true;
	}

	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
