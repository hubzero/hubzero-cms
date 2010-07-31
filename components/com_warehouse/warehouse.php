<?php
/**
 * @package    Joomla.Tutorials
 * @subpackage Components
 * components/com_warehouse/warehouse.php
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1
 * @license    GNU/GPL
*/
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("api/org/nees" . PATH_SEPARATOR . get_include_path());

spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("api/org/phpdb/propel/central/conf/central-conf.php");

// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );

//require_once('FirePHPCore/FirePHP.class.php');
//ob_start(); 

//$firephp = FirePHP::getInstance(true);
//$firephp->log('warehouse.php');

require_once 'api/org/nees/static/Search.php';
require_once 'api/org/nees/static/Experiments.php';
require_once 'api/org/nees/util/StringHelper.php';
require_once 'api/org/nees/util/FileHelper.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';
require_once 'api/org/nees/html/joomla/ViewHtml.php';
require_once 'api/org/nees/oracle/util/DbPagination.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/OrganizationPeer.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/ProjectHomepagePeer.php';
require_once 'lib/data/TrialPeer.php';
require_once 'lib/data/ExperimentFacilityPeer.php';
require_once 'lib/data/EquipmentPeer.php';
require_once 'lib/data/SpecimenPeer.php';
require_once 'neesconfiguration.php';

ximport('xprofile');
 
// Require specific controller if requested
if($controller = JRequest::getVar('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
 
// Create the controller
$classname    = 'WarehouseController'.$controller;
$controller   = new $classname( );
 
// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );
 
// Redirect if set by the controller
$controller->redirect();
