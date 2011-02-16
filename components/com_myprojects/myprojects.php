<?php
/**
 * @version		$Id: content.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("api/org/nees" . PATH_SEPARATOR . get_include_path());

spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("api/org/phpdb/propel/central/conf/central-conf.php");

// Require the com_content helper library
require_once(JPATH_COMPONENT.DS.'controller.php');

require_once 'lib/security/Authorizer.php';
require_once 'lib/security/UserManager.php';

// Component Helper
jimport('joomla.application.component.helper');

$oUser =& JFactory::getUser();

$oAuthorizer = Authorizer::getInstance();
$oAuthorizer->setUser($oUser->username);

$oUserManager = UserManager::getInstance();
$oUserManager->setUser($oUser->username);

if($oUser->username=="gemezm"){
  //$oAuthorizer->setUser("melorapark");
  //$oUserManager->setUser("melorapark");
}

// Create the controller
$controller = new MyProjectsController();

// Perform the Request task
$controller->execute(JRequest::getCmd( 'task' ));

// Redirect if set by the controller
$controller->redirect();
