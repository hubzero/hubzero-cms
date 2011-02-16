<?php
/**
 * @package    Joomla.Tutorials
 * @subpackage Components
 * components/com_projecteditor/projecteditor.php
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1
 * @license    GNU/GPL
*/
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$config = JFactory::getConfig();
if ($config->getValue('config.debug')) {
  error_reporting(E_ALL);
  @ini_set('display_errors','1');
}

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("api/org/nees" . PATH_SEPARATOR . get_include_path());

spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("api/org/phpdb/propel/central/conf/central-conf.php");

// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'projecteditor.tags.php' );
require_once( JPATH_ROOT.DS.'components'.DS.$option.DS.'groups.log.php' );

//require_once('FirePHPCore/FirePHP.class.php');
//ob_start();

//session_start(); 

require_once 'api/org/nees/static/Search.php';
require_once 'api/org/nees/static/Experiments.php';
require_once 'api/org/nees/util/StringHelper.php';
require_once 'api/org/nees/util/Tuple.php';
require_once 'api/org/nees/util/FileHelper.php';
require_once 'api/org/nees/html/TabHtml.php';
require_once 'api/org/nees/html/UserRequest.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';
require_once 'api/org/nees/html/joomla/ViewHtml.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/OrganizationPeer.php';
require_once 'lib/data/Organization.php';
require_once 'lib/data/Facility.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/ProjectHomepagePeer.php';
require_once 'lib/data/TrialPeer.php';
require_once 'lib/data/ExperimentFacilityPeer.php';
require_once 'lib/security/Authorizer.php';
require_once 'lib/security/UserManager.php';

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

$oUser =& JFactory::getUser();

$oAuthorizer = Authorizer::getInstance();
$oAuthorizer->setUser($oUser->username);

$oUserManager = UserManager::getInstance();
$oUserManager->setUser($oUser->username);
if($oUser->username=="gemezm"){
  //$oAuthorizer->setUser("rodneyp");
  //$oUserManager->setUser("rodneyp");

  //$oAuthorizer->setUser("knasi");
  //$oUserManager->setUser("knasi");

  //$oAuthorizer->setUser("cfrench");
  //$oUserManager->setUser("cfrench");

  //$oAuthorizer->setUser("blkutter");
  //$oUserManager->setUser("blkutter");

  //$oAuthorizer->setUser("sdyke");
  //$oUserManager->setUser("sdyke");

  //$oAuthorizer->setUser("mclean");
  //$oUserManager->setUser("mclean");

  //$oAuthorizer->setUser("rhowell537");
  //$oUserManager->setUser("rhowell537");
}

// Create the controller
$classname    = 'ProjectEditorController'.$controller;
$controller   = new $classname( );

// Require user to be logged in for ANY page or request of this component
if (!$controller->userloggedin())
{
  $controller->redirect();
}
else
{
  $controller->execute(JRequest::getVar( 'task' ));
  $controller->redirect();
}
