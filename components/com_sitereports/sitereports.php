<?php
/**
 * Bootstrap for the facility component. It all begins here. 
 * 
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright           Copyright 2010 by NEESCommIT
 */
error_reporting(E_ALL);
ini_set('display_errors',TRUE);

set_include_path("api/org/nees" . PATH_SEPARATOR . get_include_path());
set_include_path("api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());

spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("api/org/phpdb/propel/central/conf/central-conf.php");

require_once 'PHPExcel/Classes/PHPExcel.php';
require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once 'lib/data/FacilityPeer.php';
require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/PersonEntityRolePeer.php';
require_once 'lib/data/Role.php';
require_once 'lib/sitereportshelper.php';

require_once 'lib/data/SiteReportsSitePeer.php';
require_once 'lib/data/SiteReportsQARRPSPeer.php';
require_once 'lib/data/SiteReportsQAREotEvtPeer.php';
require_once 'lib/data/SiteReportsQARPeer.php';
require_once 'lib/data/SiteReportsQFREPcdPeer.php';
require_once 'lib/data/SiteReportsQFR.php';
require_once 'lib/data/SiteReportsQFRProjectPeer.php';
require_once 'lib/security/HubAuthorizer.php';


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Stylesheets
$doc =& JFactory::getDocument();
$doc->addStyleSheet('components'.DS.$option.DS.'sitereports.css');

// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php');
require_once( JPATH_COMPONENT.DS.'lib/sitereportshelper.php');

$controller   = new SiteReportsController();


// Set base page title
$doc->setTitle('Site Reports');

// Set base breadcrumb
$pathway =& $mainframe->getPathway();
$pathway->addItem( "Site Reports", JRoute::_('/index.php?option=com_sitereports'));


// This security model will be expanded once sites get access to run their own reports
// For now, anyone with edit permission on the SiteReportsSite entity that corresponds
// to the Org NEESIT, will get complete access to the app. For now it's all or nothing
$SiteReportsSite = SiteReportsHelper::getSiteReportsSite(1002);
$can_run = SiteReportsHelper::canRun($SiteReportsSite);

if(!$can_run)
{
    // Just redirect to the mainpage for the application
    JRequest::setVar('view','sitereports');
}

// Perform the task, generally form submissions will set this, and the corresponding
// task function will select the correct view to display depending on the outcome of
// the task
$controller->execute(JRequest::getCmd('task'));


// Redirect if set by the controller
$controller->redirect();
