<?php
set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/nees" . PATH_SEPARATOR . get_include_path());

//spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("/www/neeshub/api/org/phpdb/propel/central/conf/central-conf.php");

//require_once 'lib/data/OrganizationPeer.php';
require_once 'lib/data/Organization.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/ProjectOrganization.php';


$oProject = ProjectPeer::retrieveByPK(820);
$oOrganization = OrganizationPeer::findByName("University of Alabama at Birmingham");

$oProjectOrganizaation = new ProjectOrganization(null, $oOrganization);
$oProjectOrganizaation->setProject($oProject);
$oProjectOrganizaation->save();


?>
