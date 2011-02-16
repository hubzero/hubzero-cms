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
                                                                                                                                                  
require_once 'api/org/nees/static/Files.php';

$strReturnUrl = $_REQUEST[ProjectEditor::RETURN_URL];
$iProjectId = $_REQUEST[Files::PROJECT_ID];
$iExperimentId = $_REQUEST[Files::EXPERIMENT_ID];
$strDiv = $_REQUEST[Files::CHILD_DIV];
$strMainDiv = $_REQUEST[Files::PARENT_DIV];
$strCurrentPath = $_REQUEST[Files::CURRENT_DIRECTORY];
$strTopPath = $_REQUEST[Files::TOP_DIRECTORY];
$iRequestType = $_REQUEST[Files::REQUEST_TYPE];
$oCurrentDataFileArray = DataFilePeer::findByDirectory($strCurrentPath);
DataFilePeer::sortDataFiles($oCurrentDataFileArray);

$strUploadForm = modWarehouseUploadHelper::getFileBrowser($strMainDiv, $strDiv, $strCurrentPath, $strTopPath, $oCurrentDataFileArray, $iRequestType, true, $iProjectId, $iExperimentId, $strReturnUrl);

require(JModuleHelper::getLayoutPath('mod_warehouseupload'));