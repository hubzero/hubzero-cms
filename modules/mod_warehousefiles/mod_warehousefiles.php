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
require_once 'lib/security/UserManager.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/EntityTypePeer.php';
require_once 'lib/data/EntityType.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/curation/NCCuratedNCIDCrossRefPeer.php';
require_once 'lib/data/curation/NCCuratedNCIDCrossRef.php';
require_once 'api/org/nees/static/Files.php';
require_once 'api/org/nees/static/ProjectEditor.php';
require_once 'api/org/nees/oracle/util/DbPagination.php';
require_once 'api/org/nees/lib/common/browser.php';

$iProjectId = JRequest::getVar('projid');
$oProject = ProjectPeer::retrieveByPK($iProjectId);

$strCurrentPath = JRequest::getVar('path', '');
if(!StringHelper::hasText($strCurrentPath)){
  $strCurrentPath = $oProject->getPathname();
}

$strForm = JRequest::getVar("form", "frmData");
$strTarget = JRequest::getVar("target", "dataList");
$iTopDirectoryIndex = JRequest::getInt("top", 0);
$iExperimentId = JRequest::getInt("experimentId", 0);
$iTrialId = JRequest::getInt("trialId", 0);
$iRepetitionId = JRequest::getInt("repetitionId", 0);
$iForm = JRequest::getInt("form", 0);
$strReturnURL = JRequest::getVar('warehouseURL', "");

if($iForm){
  $iExperimentId = 0;
  $iTrialId = 0;
  $iRepetitionId = 0;
}

$iPageIndex = JRequest::getVar('index', 0);
$iDisplay = JRequest::getVar('limit', 25);

$iLowerLimit = modWarehouseFilesHelper::computeLowerLimit($iPageIndex, $iDisplay);
$iUpperLimit = modWarehouseFilesHelper::computeUpperLimit($iPageIndex, $iDisplay);

$strPagination = "";

$strType = JRequest::getVar('type', "");
switch ($strType) {
  case "Search":
    $iFindBy = JRequest::getInt('findby', 1);
    $strTerm = JRequest::getString('term', '');

    $oFileArray = null;
    $iFileCount = 0;

    if ($iFindBy==1){
      $oCurrentDataFileArray = modWarehouseFilesHelper::findByTitle($strTerm, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
      $iFileCount = modWarehouseFilesHelper::findByTitleCount($strTerm, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    }else{
      $oCurrentDataFileArray = modWarehouseFilesHelper::findByName($strTerm, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
      $iFileCount = modWarehouseFilesHelper::findByNameCount($strTerm, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    }
    DataFilePeer::sortDataFiles($oCurrentDataFileArray);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserBySearch($strTerm, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath);

    $oDbPagination = new DbPagination($iPageIndex, $iFileCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $strPagination = $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list");
    break;
  case "Chart":
    $oCurrentDataFileArray = DataFilePeer::getObjectTypeByDirectory($strCurrentPath, $strType, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $iFileCount = DataFilePeer::getObjectTypeCountByDirectory($strCurrentPath, $strType, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByType($strType, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath);

    $oDbPagination = new DbPagination($iPageIndex, $iFileCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $strPagination = $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list");
    break;
  case "Drawing":
    $oCurrentDataFileArray = DataFilePeer::findDataFileByUsageAndDirectory($strType, $strCurrentPath, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $iFileCount = DataFilePeer::findDataFileByUsageAndDirectoryCount($strType, $strCurrentPath, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByType($strType, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath);

    $oDbPagination = new DbPagination($iPageIndex, $iFileCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $strPagination = $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list");
    break;
  case "Presentation":
    $oCurrentDataFileArray = DataFilePeer::getObjectTypeByDirectory($strCurrentPath, $strType, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $iFileCount = DataFilePeer::getObjectTypeCountByDirectory($strCurrentPath, $strType, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByType($strType, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath);

    $oDbPagination = new DbPagination($iPageIndex, $iFileCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $strPagination = $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list");
    break;
  case "Publication":
    $oCurrentDataFileArray = DataFilePeer::getObjectTypeByDirectory($strCurrentPath, $strType, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $iFileCount = DataFilePeer::getObjectTypeCountByDirectory($strCurrentPath, $strType, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByType($strType, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath);

    $oDbPagination = new DbPagination($iPageIndex, $iFileCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $strPagination = $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list");
    break;
  case "Report":
    $oCurrentDataFileArray = DataFilePeer::getObjectTypeByDirectory($strCurrentPath, $strType, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $iFileCount = DataFilePeer::getObjectTypeCountByDirectory($strCurrentPath, $strType, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByType($strType, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath);

    $oDbPagination = new DbPagination($iPageIndex, $iFileCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $strPagination = $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list");
    break;
  case "Photo":
    $iFileCount = DataFilePeer::getPhotoCountByDirectory($strCurrentPath, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $oCurrentDataFileArray = DataFilePeer::getPhotoByDirectory($strCurrentPath, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $oCurrentDataFileArray = DataFilePeer::resizePhotos($oCurrentDataFileArray, false);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByMedia($strType, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath);

    $oDbPagination = new DbPagination($iPageIndex, $iFileCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $strPagination = $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list");
    break;
  case "Movie":
    /*
     * TODO: Tanzima's code goes here...
     */
    $oCurrentDataFileArray = DataFilePeer::getMovieByDirectory($strCurrentPath, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $iFileCount = DataFilePeer::getMovieCountByDirectory($strCurrentPath, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByType($strType, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath);

    $oDbPagination = new DbPagination($iPageIndex, $iFileCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $strPagination = $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list");
    break;
  case "DataFile":
    $strTool = JRequest::getVar("tool", "");

    $oCurrentDataFileArray = DataFilePeer::getDataFileByDirectory($strCurrentPath, $strTool, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
    DataFilePeer::sortDataFiles($oCurrentDataFileArray);
    $iFileCount = DataFilePeer::getDataFileCountByDirectory($strCurrentPath, $strTool, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);

    if(StringHelper::hasText($strTool)){
      $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByInDeed($strType, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath, $strReturnURL);
    }else{
      $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByType($strType, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strCurrentPath);
    }

    $oDbPagination = new DbPagination($iPageIndex, $iFileCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $strPagination = $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list");
    break;
  case "Ajax":
    $oCurrentDataFileArray = DataFilePeer::findByDirectory($strCurrentPath);
    DataFilePeer::sortDataFiles($oCurrentDataFileArray);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowserByAjax($strCurrentPath, $oCurrentDataFileArray, $iTopDirectoryIndex, $iProjectId, $strForm, $strTarget, $strReturnURL);
    break;
  default:
    $oCurrentDataFileArray = DataFilePeer::findByDirectory($strCurrentPath);
    DataFilePeer::sortDataFiles($oCurrentDataFileArray);
    $strDataFilesHTML = modWarehouseFilesHelper::getFileBrowser($strCurrentPath, $oCurrentDataFileArray, $oProject->getPathname(), $iProjectId);
    break;
}



require(JModuleHelper::getLayoutPath('mod_warehousefiles'));
