<?php
/**
* @version		$Id: mod_login.php 10381 2008-06-01 03:35:53Z pasamio $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');
  	
$oDataFileArray = null;
$strPathArray = null;
  
$strPath = JRequest::getVar("path","");
  
$strPathArray = explode("/", $strPath);

$strBackPath = null;
if(!StringHelper::endsWith($strPath, ".groups")){
  $strBackArray = array_diff($strPathArray, array(array_pop($strPathArray)));
  
  $strBackPath = implode("/", $strBackArray);
}
  
$strIncludedFolderArray = array("Documentation", "Public", "Analysis");
$oDataFileArray = modWarehouseDocsHelper::getDocumentSummary($strPath, $strIncludedFolderArray);
    
$_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFileArray);

require(JModuleHelper::getLayoutPath('mod_warehousedocs'));