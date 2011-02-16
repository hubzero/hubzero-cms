<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_chile_damage()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	$dd['title'] = "The Chile Earthquake Database: Damage";
	$dd['table'] = 'chile_damage';
	$dd['db'] = array('host'=> 'stage.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
	
	$dd['joins'][] = array('table'=>'chile_buildings', 'ids'=>array('chile_buildings.Building', 'chile_damage.Building'));
	
	$dd['cols']['chile_damage.Number'] = array('hide'=>'hide');
	$dd['cols']['chile_buildings.Number'] = array('hide'=>'hide');
	$dd['cols']['chile_damage.Building'] = array('label'=>'Building', 'more_info'=>'chile_buildings|chile_buildings.Number');
	$dd['cols']['chile_damage.Year'] = array('label'=>'Year');
	$dd['cols']['chile_damage.main_pic'] = array('label'=>'Photographs', 'type'=>'image', 'gallery'=>'chile_damage.Photographs', 'resized'=>'resized');		// IF(me==sleeping) THEN pls update the fieldname and uncomment to use the mainpic when it's added
//	$dd['cols']['"/site/collections/haiti/mainpics/small/A001.JPG"'] = array('label'=>'Photographs', 'type'=>'image', 'gallery'=>'chile_damage.Photographs');  // temp. gallery column
	$dd['cols']['chile_damage.sd_none'] = array('label'=>'Structural Damage<br />[None]');
	$dd['cols']['chile_damage.sd_light'] = array('label'=>'Structural Damage<br />[Light]');
	$dd['cols']['chile_damage.sd_moderate'] = array('label'=>'Structural Damage<br />[Moderate]');
	$dd['cols']['chile_damage.sd_severe'] = array('label'=>'Structural Damage<br />[Severe]');
	$dd['cols']['chile_damage.sd_mean'] = array('label'=>'Structural Damage<br />[Mean]');
	$dd['cols']['chile_damage.nsd_none'] = array('label'=>'Nonstructural Damage<br />[None]');
	$dd['cols']['chile_damage.nsd_light'] = array('label'=>'Nonstructural Damage<br />[Light]');
	$dd['cols']['chile_damage.nsd_moderate'] = array('label'=>'Nonstructural Damage<br />[Moderate]');
	$dd['cols']['chile_damage.nsd_severe'] = array('label'=>'Nonstructural Damage<br />[Severe]');
	$dd['cols']['chile_damage.nsd_man'] = array('label'=>'Nonstructural Damage<br />[Mean]');
	$dd['cols']['chile_damage.Photographs'] = array('hide'=>'hide');
	$dd['cols']['chile_damage.Crack_Maps'] = array('label'=>'Crack Maps');
	$dd['cols']['chile_damage.Cost_of_Repair'] = array('label'=>'Cost of Repair', 'desc'=>'Chilean Pesos at time of Repair');
	
	if ($id) {
		$dd['where'][] = array('field'=>'chile_buildings.Number', 'value'=>$id);
		$dd['single'] = true;
	}

	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
