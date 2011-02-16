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

require_once 'lib/security/Authorizer.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'api/org/nees/static/Files.php';
require_once 'api/org/nees/static/ProjectEditor.php';

$iProjectId = JRequest::getVar('projid');
$oProject = ProjectPeer::find($iProjectId);

$strCurrentPath = JRequest::getVar('path', '');
if(!StringHelper::hasText($strCurrentPath)){
  $strCurrentPath = $oProject->getPathname();
} 

$iDataFileCount = modWarehouseFileTypesHelper::getDataFileCountByDirectory($strCurrentPath);
$iInDeedDataFileCount = modWarehouseFileTypesHelper::getDataFileCountByDirectory($strCurrentPath, "inDEED");
$iDataFileAllCount = $iDataFileCount - $iInDeedDataFileCount;
$iDrawingCount = modWarehouseFileTypesHelper::getDrawingCountByDirectory($strCurrentPath);
$iPhotoCount = modWarehouseFileTypesHelper::getPhotoCountByDirectory($strCurrentPath);
$iChartCount = modWarehouseFileTypesHelper::getObjectTypeCountByDirectory($strCurrentPath, "Chart");
$iPresentationCount = modWarehouseFileTypesHelper::getObjectTypeCountByDirectory($strCurrentPath, "Presentation");
$iPublicationCount = modWarehouseFileTypesHelper::getObjectTypeCountByDirectory($strCurrentPath, "Publication");
$iReportCount = modWarehouseFileTypesHelper::getObjectTypeCountByDirectory($strCurrentPath, "Report");
$iFrameCount = modWarehouseFileTypesHelper::getFrameCaptureCountByDirectory($strCurrentPath);
$iMovieCount = modWarehouseFileTypesHelper::getMovieCountByDirectory($strCurrentPath);
$iVideoCount = $iMovieCount + $iFrameCount;

require(JModuleHelper::getLayoutPath('mod_warehousefiletypes'));
