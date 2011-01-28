<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

function get_sasw()
{
	$link = get_db();
	$id = isset($_REQUEST['id'])? mysql_real_escape_string($_REQUEST['id']): false;

	//Data definition
	$dd['title'] = "Shear Wave Velocity Database";
	$dd['table'] = 'saswdb';
	//$dd['db'] = array('host'=> 'nees.org', 'user'=>'nistequser', 'pass' => '_nist3QkE_', 'name' => 'nistearthquakedata');
	$dd['db'] = array('host'=> 'neesud.neeshub.org', 'user'=>'userDB', 'pass' => 'userDB1_pass', 'name' => 'earthquakedata');

	$dd['cols']['saswdb.Counter'] = array('label'=>'ID', 'desc'=>'Database ID', 'data_type'=>'int', 'more_info'=>'sasw|saswdb.Counter', 'compare'=>'Select two or more items to compare side-by-side', 'width'=>'50');
	$dd['cols']['saswdb.NEESR_ID'] = array('label'=>'NEESR-ID', 'hide'=>'hide');
	$dd['cols']['saswdb.NEESR_ID_link'] = array('label'=>'Reference<br />/NEESR-ID-link', 'type'=>'link', 'link_label'=>'saswdb.NEESR_ID', 'desc'=>'NEESR ID (if applicable)', 'width'=>'80');
	$dd['cols']['saswdb.ProjectPI'] = array('label'=>'Project PI', 'desc'=>'Principal Investigator(s) for the Project', 'width'=>'80');
	$dd['cols']['saswdb.Location_State'] = array('label'=>'State', 'desc'=>'State of test site.', 'width'=>'30');
	$dd['cols']['saswdb.Location_City'] = array('label'=>'City', 'desc'=>'Nearest city to test site.', 'width'=>'50');
	$dd['cols']['saswdb.Location_Name'] = array('label'=>'Test Site<br />Name', 'desc'=>'Name used by researchers for test site.', 'width'=>'60');
	$dd['cols']['saswdb.Latitude_full'] = array('label'=>'Latitude', 'desc'=>'Latitude', 'raw'=>"CONCAT(saswdb.Lat_Deg,'° ', saswdb.Lat_Min,\"' \", saswdb.Lat_Sec,'\" ', saswdb.Lat_Dir)", 'width'=>'80');
	$dd['cols']['saswdb.Longitude_full'] = array('label'=>'Longitude', 'desc'=>'Longitude', 'raw'=>"CONCAT(saswdb.Long_Deg,'° ', saswdb.Long_Min,\"' \", saswdb.Long_Sec,'\" ', saswdb.Long_Dir)", 'width'=>'90');
	$dd['cols']['saswdb.VelocityVsDepth'] = array('label'=>'Vs Profile (CSV)', 'desc'=>'Shear wave velocity profile obtained from tests.', 'width'=>'240', 'type'=>'tool', 'name'=>'inDEED', 'link_format'=>'https://nees.org/tools/indeed/invoke?list={p}', 'param'=>'saswdb.VelocityVsDepth', 'dl'=>'saswdb.VelocityVsDepth', 'width'=>'200');
	$dd['cols']['saswdb.VelocityImage'] = array('label'=>'Vs Profile', 'desc'=>'Vs Profile Image', 'type'=>'image');
	$dd['cols']['saswdb.DispersionCurve'] = array('label'=>'Dispersion<br />Curve', 'desc'=>'Dispersion Curve Image', 'type'=>'image');
	$dd['cols']['saswdb.Vs30'] = array('label'=>'Vs30<br />(m/s)', 'desc'=>'Average shear wave velocity.');
	$dd['cols']['saswdb.NEHRP_SiteClass'] = array('label'=>'NEHRP<br />Site Class', 'desc'=>'Site class based on NEHRP specifications.');
	$dd['cols']['saswdb.Depth_of_Profile'] = array('label'=>'Profile Depth<br />(m)', 'desc'=>'Depth of profile taken during testing.');
	$dd['cols']['saswdb.Geology'] = array('label'=>'Geology', 'Geology of test site.');
	$dd['cols']['saswdb.Profile_Image'] = array('label'=>'Profile', 'desc'=>'Image of soil profile at test site.', 'type'=>'image');
	$dd['cols']['saswdb.TestingType'] = array('label'=>'Test Procedure', 'desc'=>'Type of test procedure used.' , 'width'=>'230');
	$dd['cols']['saswdb.EquipmentType'] = array('label'=>'Equipment', 'desc'=>'Type of equipment used during testing.', 'width'=>'230');
	$dd['cols']['saswdb.ComputerProgram'] = array('label'=>'Computer<br />Program', 'desc'=>'Type of computer program used for analysis.');
	$dd['cols']['saswdb.StartDate'] = array('label'=>'Start', 'desc'=>'Start date of testing.');
	$dd['cols']['saswdb.EndDate'] = array('label'=>'End', 'desc'=>'End date of testing.');
	$dd['cols']['saswdb.Weather'] = array('label'=>'Weather', 'desc'=>'Weather conditions during testing.');

	$dd['show_maps'] = true;
	$dd['maps'][] = array('title'=>'saswdb.Location_Name', 'lat'=>'saswdb.Latitude_full', 'lng'=>'saswdb.Longitude_full', 'cood_type'=>'dms');

	if ($id) {
		$dd['where'][] = array('field'=>'saswdb.Counter', 'value'=>$id);
		$dd['single'] = true;
	}

	$sql = query_gen($dd);

	$res = get_results($sql, $dd);

	return $res;
}
?>
