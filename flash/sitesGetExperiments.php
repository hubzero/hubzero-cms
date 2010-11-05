<?php

//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/nees" . PATH_SEPARATOR . get_include_path());

//spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("/www/neeshub/api/org/phpdb/propel/central/conf/central-conf.php");

require_once("/www/neeshub/api/org/phpdb/propel/central/classes/lib/data/FacilityPeer.php");
require_once("/www/neeshub/api/org/phpdb/propel/central/classes/lib/data/NAWIFacilityPeer.php");





$facilities = FacilityPeer::findAllFacilities();

ob_clean();
header("Content-type: text/xml");

$output = <<<ENDXML
<?xml version="1.0" encoding="iso-8859-1" ?>
<ExperimentList>

ENDXML;

$nawifacs = NAWIFacilityPeer::findUpcomingExperiments();

$upcoming = "";

foreach ($nawifacs as $nawifac) {
  /* @var $nawifac NAWIFacility */
  $fac = $nawifac->getOrganization();
  $nawi = $nawifac->getNAWI();
  $nawiid = $nawi->getId();
  $fid = $fac->getId();
  $facName = $fac->getShortName();
  $eDate = cleanDate($nawi->getTestDate());
  $testTimeZone = $nawi->getTestTimeZone();
  $experimentName = $nawi->getExperimentName();


  $output .= <<<ENDXML

  <facility>
    <Name>$facName</Name>
    <facid>$fid</facid>
    <TestDate>$eDate</TestDate>
    <TimeZone>$testTimeZone</TimeZone>
    <ExperimentName>$experimentName</ExperimentName>
    <nawiid>$nawiid</nawiid>
  </facility>

ENDXML;

}

$output .= <<<ENDXML
</ExperimentList>
ENDXML;

echo $output;
exit;



function cleanDate( $uglyDate, $shortDate = 0 ) {
  if(is_null($uglyDate) || ($uglyDate == "") || preg_match ('/0{1,4}-00-00.*/', $uglyDate)) {
    return "TBD";
  } elseif(preg_match ('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $uglyDate)) {
    if($shortDate) {
      return date("m/d/y", strtotime($uglyDate));
    } else {
      return date("M j, Y \a\\t h:i a", strtotime($uglyDate));
    }
  } elseif(preg_match ('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $uglyDate)) {
    if($shortDate) {
      return date("m/d/y", strtotime($uglyDate));
    } else {
      return date("M j, Y", strtotime($uglyDate));
    }
  }
}


?>
