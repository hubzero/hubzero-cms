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
spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("api/org/phpdb/propel/central/conf/central-conf.php");

// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );

require_once('FirePHPCore/FirePHP.class.php');
ob_start();

session_start(); 

require_once 'api/org/nees/static/Search.php';
 
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
