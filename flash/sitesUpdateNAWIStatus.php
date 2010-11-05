<?php

//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/nees" . PATH_SEPARATOR . get_include_path());

//spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("/www/neeshub/api/org/phpdb/propel/central/conf/central-conf.php");

require_once("/www/neeshub/api/org/phpdb/propel/central/classes/lib/data/FacilityPeer.php");
require_once("/www/neeshub/api/org/nees/lib/security/Authenticator.php");
require_once("/www/neeshub/api/org/nees/lib/security/GridAuth.php");
require_once('/www/neeshub/api/org/nees/lib/security/HubAuthorizer.php');
require_once('/www/neeshub/api/org/nees/lib/security/PermissionsViewPeer.php');


//$authenticator = Authenticator::getInstance();
/* @var $gridAuth GridAuth */
$gridAuth = new GridAuth();
$GASession = isSet($_REQUEST['GAsession']) ? $_REQUEST['GAsession'] : '';


## Get the numeric facility id, based on either the facility's id, or its short name.
$fid = array_key_exists('facid', $_REQUEST) ? $_REQUEST["facid"] : null;
$nid = array_key_exists('nawiid', $_REQUEST) ? $_REQUEST["nawiid"] : null;
$nawifac = null;

if (!is_null($fid)) {
    $nawifac = FacilityPeer::find($fid);
    if (is_null($nawifac)) {
        $fid = null; // $fid provided invalided, reset it to null
    } else {
        // User may provided the shortName, instead of ID for $fid
        // Set this variable to the numeric facility ID from now on.
        $fid = $nawifac->getId();
    }
}




################################################################################
## Edit Experiment Information (doit)
################################################################################
if (isset($_REQUEST['doit']) && $_REQUEST['doit'] == "2") {

    // This seciton attempts to get logged in user information for those who pass a GASessionID
    // on the query string
    $can_edit = false;
    if(!empty($GASession))
    {
        $login = $gridAuth->login($GASession);

        // Get the logged in username from the session
        $sessionUserName = '';
        $sessionUserName = $gridAuth->getUsernameFromSession($GASession);

        if(empty($sessionUserName))
        {
            echo '<error>Cannot find Username for Session: ' . $GASession . '</error>';
            return;
        }


        $person = PersonPeer::findByUserName($sessionUserName);


        if(empty($person))
        {
            echo '<error>Cannot find person:' . $sessionUserName . '</error>';
            return;
        }

        $can_edit = PermissionsViewPeer::canDo($person->getId(), $fid, DomainEntityType::ENTITY_TYPE_FACILITY, 'EDIT');
    }


    // Only allow those who can edit this faciltiy to change the NAWI status ID
    if($can_edit)
    {
        // See if user has edit priviledges on this facility
        // $sessionUserName, $facid

        if (isset($_REQUEST['optstat'])) {
            $optStat = preg_replace("/[\'\"\\\|]/", "", $_REQUEST['optstat']);
            if ($optStat == "NEES" || $optStat == "NON_NEES" || $optStat == "FLEX" || $optStat == "SHARED") {
                FacilityPeer::updateNAWI_Status($fid, $optStat);
            }
        }
    }
    else
    {
        echo '<error>User does not have edit site permission to change NAWI status. GASessionID: ' . $GASession . '</error>';
    }

}




if (isset($_REQUEST['eloc']) && $_REQUEST['eloc'] == 'getNawiStatus')
{
    if ($nawifac) {
        $facName = $nawifac->getShortName();
        $stat = $nawifac->getNawiStatus();
    } else {
        $facName = "UNKNOWN FACILITY";
        $stat = "UNKNOWN STATUS";
    }

    header("Content-type: text/xml");

    $output = <<<ENDXML
<?xml version="1.0" encoding="iso-8859-1" ?>
<nawiStatus>
  <facility>$facName</facility>
  <facid>$fid</facid>
  <status>$stat</status>
</nawiStatus>

ENDXML;

    echo $output;
    exit;
}
elseif (isset($_REQUEST['eloc']) && $_REQUEST['eloc'] == 'getAllNawiStatus')
{

    $facilities = FacilityPeer::findAllFacilities();

    ob_clean();
    header("Content-type: text/xml");

    $output = <<<ENDXML
<?xml version="1.0" encoding="iso-8859-1" ?>
<nawiStatus>

ENDXML;

    foreach ($facilities as $nawifac)
    {
        $facName = $nawifac->getShortName();
        $fid = $nawifac->getId();
        $stat = $nawifac->getNawiStatus();

        $output .= <<<ENDXML

        <facility>
            <name>$facName</name>
            <facid>$fid</facid>
            <status>$stat</status>
        </facility>
ENDXML;
    }

$output .= <<<ENDXML
</nawiStatus>
ENDXML;

echo $output;
exit;
}
?>
