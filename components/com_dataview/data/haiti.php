<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_haiti()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	//Data definition
	$dd['title'] = "The Haiti Earthquake Database";
	$dd['table'] = 'haitidb';
	//$dd['db'] = array('host'=> 'nees.org', 'user'=>'nistequser', 'pass' => '_nist3QkE_', 'name' => 'nistearthquakedata');
	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');
//	$dd['serverside'] = true;

	$dd['cols']['haitidb.No_'] = array('label'=>'Number');
	$dd['cols']['haitidb.Building'] = array('label'=>'Building', 'desc'=>'The Building Identification Marker.');
	$dd['cols']['haitidb.Date'] = array('label'=>'Date', 'desc'=>'Date on which the building was examined.');
	$dd['cols']['haitidb.Team'] = array('label'=>'Team', 'desc'=>'The team that examined the building.');
	$dd['cols']['haitidb.Latitude'] = array('label'=>'Latitude', 'desc'=>'Building latitude.', 'raw'=>"CONCAT(haitidb.LatitudeD,'° ', haitidb.LatitudeM,\"' \",haitidb.LatitudeS,'\"', ' N')", 'width'=>'100');
	$dd['cols']['haitidb.Longitude'] = array('label'=>'Longitude', 'desc'=>'Building Logitude.', 'raw'=>"CONCAT(haitidb.LongitudeD,'° ', haitidb.LongitudeM,\"' \",haitidb.LongitudeS,'\"', ' W')", 'width'=>'100');
	$dd['cols']['haitidb.PicCollection'] = array('hide'=>'hide');
	$dd['cols']['haitidb.MainPic'] = array('label'=>'Pictures', 'desc'=>'Pictures of thebuilding taken durring the examination.', 'type'=>'image', 'gallery'=>'haitidb.PicCollection', 'resized'=>'resized');
	$dd['cols']['haitidb.DiagramPreview'] = array('hide'=>'hide');
	$dd['cols']['haitidb.Diagram'] = array('label'=>'Diagram', 'desc'=>'Diagram of the first floor of the building.', 'type'=>'link', 'width'=>'150', 'multi'=>'ul', 'sep'=>',', 'preview'=>'haitidb.DiagramPreview');
	$dd['cols']['haitidb.PriorityIndex1'] = array('label'=>'Priority Index<br />[%]', 'desc'=>'The Priority Index of the Building.');
	$dd['cols']['haitidb.Floors'] = array('label'=>'Number of<br />Floors', 'desc'=>'Number of floors in the building.');
	$dd['cols']['haitidb.Roof'] = array('label'=>'Roof<br />Type', 'desc'=>'Mateiral used in conftructing the roof.');
	$dd['cols']['haitidb.1FloorArea'] = array('label'=>'First Floor Area<br />[ft²]', 'desc'=>'Area of First floor.');
	$dd['cols']['haitidb.TotalFloorArea'] = array('label'=>'Total Floor Area<br />[ft²]', 'desc'=>'Total Floor area');
	$dd['cols']['haitidb.ColumnArea'] = array('label'=>'Column Area<br />[ft²]', 'desc'=>'Total cross-sectional column area on the first floor.');
	$dd['cols']['haitidb.Ace'] = array('label'=>'Ace<br />[ft²]', 'desc'=>'Efective Cloumn Area<br />[ft²]', 'desc'=>'Efective cloumn area.');
	$dd['cols']['haitidb.CWAreaNS'] = array('label'=>'Concrete Wall<br />Area N-S [ft²]', 'desc'=>'Total cross-sectioanl concreate wall area in the North-South direction.');
	$dd['cols']['haitidb.CWAreaEW'] = array('label'=>'Concrete Wall<br />Area E-W [ft²]', 'desc'=>'Total cross-sectioanl concreate wall area in the East-West direction.');
	$dd['cols']['haitidb.MWAreaNS'] = array('label'=>'Masonry Wall<br />Area N-S [ft²]', 'desc'=>'The total cross-sectioanl masonry wall area in the North-South direction.');
	$dd['cols']['haitidb.MWAreaEW'] = array('label'=>'Masonry Wall<br />Area E-W [ft²]', 'desc'=>'The total cross-sectioanl masonry wall area in the East-West direction.');
	$dd['cols']['haitidb.AWt'] = array('label'=>'Effective<br />Wall Area', 'desc'=>'The effective wall area.');
	$dd['cols']['haitidb.WallIndex'] = array('label'=>'Wall Index<br />[%]', 'desc'=>'The wall index');
	$dd['cols']['haitidb.ColumnIndex'] = array('label'=>'Column Index<br />[%]', 'desc'=>'The Column index.');
	$dd['cols']['haitidb.PriorityIndex2'] = array('label'=>'Priority Index<br />[%]', 'desc'=>'The Priority Index of the Building.');
	$dd['cols']['haitidb.RCDamage'] = array('label'=>'Reinforced<br />Concrete Damage', 'desc'=>'Amount of damage observed in the building\'s reinforced concrete.');
	$dd['cols']['haitidb.MWDamage'] = array('label'=>'Masonry Wall<br />Damage', 'desc'=>'Amount of damage observed in the building\'s masonry walls.');
	//$dd['cols']['haitidb.CWDamage'] = array('label'=>'Concrete Wall<br />Damage', 'desc'=>'Amount of damage observed in the building\'s concrete walls.');
	$dd['cols']['haitidb.Photographer'] = array('label'=>'Photographer', 'desc'=>'Name of primaty photographer.');
	$dd['cols']['haitidb.PermenantDrift'] = array('label'=>'Permenant<br />Drift', 'desc'=>'Observed permanante drift.');
	$dd['cols']['haitidb.CaptiveColumns'] = array('label'=>'Captive<br />Columns', 'desc'=>'Observed captive columns');
	$dd['cols']['haitidb.Notes'] = array('label'=>'Notes', 'width'=>'200', 'truncate'=>'truncate');

	$dd['show_maps'] = true;
	$dd['maps'][] = array('title'=>'haitidb.Building', 'lat'=>'haitidb.Latitude', 'lng'=>'haitidb.Longitude', 'cood_type'=>'dms');

	if ($id) {
		$dd['where'][] = array('field'=>'haitidb.No_', 'value'=>$id);
		$dd['single'] = true;
	}

	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
