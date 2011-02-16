<?php
/**
* @version		$Id: helper.php 11668 2009-03-08 20:33:38Z willebil $
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

class modWarehouseFilesHelper{

  public function isViewable($p_oDataFile) {
    $bCanAccessFile = false;

    if ($p_oDataFile->getView() == "PUBLIC") {
        $bCanAccessFile = true;
    } elseif (preg_match("/\/nees\/home\/(facility.groups|Facilities.groups)/", $p_oDataFile->getPath())) {
        $bCanAccessFile = true;
    } elseif ($p_oDataFile->isInPublicDir()) {
        $bCanAccessFile = true;
    } else {
        $oEntity = $p_oDataFile->getOwner();
        if ($oEntity) {
            if ($oEntity->isPublished()) {
                $bCanAccessFile = true;
            } else {
                $oUserManager = UserManager::getInstance();
                if ($oUserManager->isMember($oEntity)) {
                    $bCanAccessFile = true;
                }
            }
        }
    }

    return $bCanAccessFile;
  }

  public function computeLowerLimit($p_iPageIndex, $p_iDisplay) {
    if ($p_iPageIndex == 0) {
        return 1;
    }
    return ($p_iDisplay * $p_iPageIndex) + 1;
  }

  public function computeUpperLimit($p_iPageIndex, $p_iDisplay) {
    if ($p_iPageIndex == 0) {
        return $p_iDisplay;
    }
    return $p_iDisplay * ($p_iPageIndex + 1);
  }

  public function getFileBrowser($p_strCurrentPath, $p_oCurrentDataFileArray, $p_strTopPath, $p_iProjectId){
    $strReturn = "";

    //create a friendly looking path
    $strCurrentFriendlyPath = get_friendlyPath($p_strCurrentPath);

    //provide breadcrumbs for file browser
    $strLocationPath = "";
    $strLocationArray = explode("/", $strCurrentFriendlyPath);
    array_shift($strLocationArray);

    $strLocationLinks = "";
    foreach($strLocationArray as $iLinkIndex=>$strLocation){
      $strLocationLinks .= "/".$strLocation;
      $strLocationLinks = get_systemPath($strLocationLinks);

      $strLocationPath = $strLocationPath .<<< ENDHTML
       / <a href="/warehouse/filebrowser/$p_iProjectId?path=$strLocationLinks&toppath=$p_strTopPath">
            $strLocation
         </a>
ENDHTML;
    }

    $iMaxDownload = 500000000;
    $strMaxDownload = "500 MB";

    $strReturn .= <<< ENDHTML
              <div  id="projectDocs">
              <table cellpadding="1" cellspacing="1" style="margin-top:15px;">
                <thead>
                  <th valign="top" colspan="6" style="white-space:normal">
                    Location:&nbsp;&nbsp;
                    <span style="font-weight:normal;">$strLocationPath</span>
                  </th>
                </thead>

                <tr>
                  <td width="1"><input id="checkAll" type="checkbox" name="checkAll" onClick="setAllDownloadCheckBoxes('frmData', 'cbxDataFile[]', this.checked, $p_iProjectId, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                  <td width="1"></td>
                  <td><b>Name</b></td>
                  <td><b>Size</b></td>
                  <td><b>Timestamp</b></td>
                  <td><b>Application</b></td>
                </tr>
ENDHTML;

          $iIndex = 0;
          if(!empty($p_oCurrentDataFileArray)){
            /* @var $oDataFile DataFile */
            foreach($p_oCurrentDataFileArray as $oDataFile){
              $strCurationStatus = $oDataFile->getCurationStatus();
              if($strCurationStatus==ProjectEditor::CURATION_REQUEST){
                $strCurationStatus = "<span class='curateSubmitted'>*</span>";
              }else{
                $strCurationStatus = "";
              }

              $strFileTimestamp = $oDataFile->getCreated();
              $iDataFileId = $oDataFile->getId();
              $strFilePath = $oDataFile->getPath();
              $strFileName = $oDataFile->getName();
              $strDataFileLinkTitle = "";

              $oDataFileLink = DataFileLinkPeer::retrieveByPK($iDataFileId);
              /* @var $oDataFileLink DataFileLink */
              if(preg_match("/Experiment-([0-9])+/", $strFileName)){
                $strDataFileLinkTitle = StringHelper::neat_trim($oDataFileLink->getExperiment()->getTitle(), 50);
              }

              if(preg_match("/Trial-([0-9])+/", $strFileName)){
                $strDataFileLinkTitle = StringHelper::neat_trim($oDataFileLink->getTrial()->getTitle(), 50);
              }

              /*
               * Get the tooltip for this file.
               * a) desc
               * b) title
               * c) timestamp
               */
              $strTooltip = (StringHelper::hasText($oDataFile->getDescription())) ? $oDataFile->getDescription() : $oDataFile->getTitle();
              if(!StringHelper::hasText($strTooltip)){
                $strTooltip =  "Timestamp: ".$strFileTimestamp;
              }

              /*
               * Figure out if we have a jpg, png, or gif.
               * If so, link to the 800x600 file
               */
              $strFileNameParts = explode('.', $strFileName);
              $strFileExtension = array_pop($strFileNameParts);

              //validate extension
              $extOk = false;
              $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
              foreach($validFileExts as $key => $value){
                if( preg_match("/$value/i", $strFileExtension ) ){
                  $extOk = true;
                  break;
                }

              }

              $strFileFriendlyPath = $oDataFile->getFriendlyPath();
              $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();

              //determine if scaled image is available
              $strLightbox = StringHelper::EMPTY_STRING;
              if($extOk){
                $strDisplayName = "display_".$iDataFileId."_".$strFileName;
                $strDisplayPath = $strFilePath."/".Files::GENERATED_PICS;

                if(is_file($strDisplayPath."/".$strDisplayName)){
                  $oDataFile->setName($strDisplayName);
                  $oDataFile->setPath($strDisplayPath);
                  $strLightbox = "rel=lightbox[data] ";

                  $strFileFriendlyPath = $oDataFile->getFriendlyPath();
                  $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();
                }
              }
              

              //default the link to a file.  if an image, put in lightbox (png,jpg,gif)
              $strLinkToFileUrl = $oDataFile->getUrl();
              $strThisLink = <<< ENDHTML
                <a title="$strTooltip"
                   href="$strLinkToFileUrl" $strLightbox>
                  $strFileName
                </a>
                $strCurationStatus
ENDHTML;


              /*
               * If we're dealing with a directory,
               * get the number of files and sub-folders.
               * Also, link to the data view of the project
               * editor, not component com_data.
               */
              $strFileCount = "";
              $strDirectoryCount = "";
              $strDirectory = "";
              $iDirectorySize = ($oDataFile->isDirectory()) ? DataFilePeer::getDirectorySize($oDataFile->getFullPath()) : 0;
              $iFileSize = (!$oDataFile->isDirectory()) ? $oDataFile->get_friendlySize() : "";
              $strFileCreated = ($oDataFile->getCreated()) ? $oDataFile->getCreated() : "";
              $strTool = self::getTool($oDataFile);
              $strIcon = self::getIcon($oDataFile);

              if( $oDataFile->getDirectory()==1){
                if(StringHelper::hasText($strDataFileLinkTitle)){
                  $strDataFileLinkTitle = ": ".$strDataFileLinkTitle;
                }

                if($iDirectorySize > 0){
                  $iFileSize = cleanSize($iDirectorySize);

                  $strDirectory = <<< ENDHTML
                    <a style="border:0px;" title="$strTooltip"
                       href="/warehouse/filebrowser/$p_iProjectId?path=$strFileSystemPath&toppath=$p_strTopPath">
                      $strIcon
                    </a>
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <a title="$strTooltip"
                      href="/warehouse/filebrowser/$p_iProjectId?path=$strFileSystemPath&toppath=$p_strTopPath">
                      $strFileName $strDataFileLinkTitle
                    </a>
                    $strCurationStatus
ENDHTML;
                }else{
                  $strDirectory = <<< ENDHTML
                    $strIcon
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <span class="emptyFolder">
                      $strFileName $strDataFileLinkTitle (0 files)
                    </span>
                    $strCurationStatus
ENDHTML;
                }
              }

              //validate if data file on file system
              if($oDataFile->existsInFilesystem()){
                //exclude generated pics from the listing
                if($strFileName != Files::GENERATED_PICS && self::isViewable($oDataFile)){
                  $strBgColor = ($iIndex%2==0) ? "even" : "odd";
                  ++$iIndex;

                  $strThisIcon = ($strDirectory=="") ? $strIcon : $strDirectory;


                  $strLaunchInEED = NeesConfig::LAUNCH_INDEED;

                  $strReturn .= <<< ENDHTML
                    <tr class="$strBgColor">
                      <td><input id="$p_iProjectId" type="checkbox" name="cbxDataFile[]" value="$iDataFileId" onClick="computeDownloadSize(this, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                      <td>$strThisIcon</td>
                      <td>$strThisLink</td>
                      <td>$iFileSize</td>
                      <td>$strFileCreated</td>
                      <td>$strTool</td>
                    </tr>
ENDHTML;
                }//end .Generated_Pics
              }
            }//end foreach
          }else{
            $strReturn .= <<< ENDHTML
            <tr>
              <td colspan="4">
                No files found.
              </td>
            </tr>
ENDHTML;
          }

    $strDownloadDiv = self::getDownloadDiv($iMaxDownload, $strMaxDownload);

    $strReturn .= <<< ENDHTML
                </table>
                <br>
                $strDownloadDiv
                </div>
ENDHTML;

    return $strReturn;

  }

  public function getFileBrowserByType($p_strType, $p_oCurrentDataFileArray, $p_strTopPath, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0, $p_strCurrentPath=""){
    $strExperimentFilter = self::getExperimentFilter($p_iProjectId, $p_iExperimentId);
    $strTrialFilter = self::getTrialFilter($p_iExperimentId, $p_iTrialId);
    $strRepetitionFilter = self::getRepetitionFilter($p_iTrialId, $p_iRepetitionId);

    $strReturn = <<< ENDHTML
                    <input type="hidden" name="path" value="$p_strCurrentPath"/>
                    <input type="hidden" name="type" value="$p_strType"/>
                    <div id="fileBrowserFilters" style="float:right">
                      <div class="editorInputFloat editorInputMargin">
                        Filter:
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strExperimentFilter
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strTrialFilter
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strRepetitionFilter
                      </div>
                      <div class="clearFloat"></div>
                    </div>
                    <div class="clearFloat"></div>
ENDHTML;

    $iMaxDownload = 500000000;
    $strMaxDownload = "500 MB";

    $strReturn .= <<< ENDHTML
              <table cellpadding="1" cellspacing="1" style="margin-top:15px;">
                <thead>
                  <th valign="top" colspan="6" style="white-space:normal">
                    File Type:&nbsp;&nbsp;
                    <span style="font-weight:normal;">$p_strType</span>
                  </th>
                </thead>

                <tr>
                  <td width="1"><input id="checkAll" type="checkbox" name="checkAll" onClick="setAllDownloadCheckBoxes('frmData', 'cbxDataFile[]', this.checked, $p_iProjectId, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                  <td width="1"></td>
                  <td><b>Name</b></td>
                  <td><b>Size</b></td>
                  <td><b>Path</b></td>
                </tr>
ENDHTML;

          if(!empty($p_oCurrentDataFileArray)){
            $iIndex = 0;

            /* @var $oDataFile DataFile */
            foreach($p_oCurrentDataFileArray as $oDataFile){
              $strCurationStatus = $oDataFile->getCurationStatus();
              if($strCurationStatus==ProjectEditor::CURATION_REQUEST){
                $strCurationStatus = "<span class='curateSubmitted'>*</span>";
              }else{
                $strCurationStatus = "";
              }

              $strFileTimestamp = $oDataFile->getCreated();
              $iDataFileId = $oDataFile->getId();
              $strFilePath = $oDataFile->getPath();
              $strFileName = $oDataFile->getName();
              $strDirPath = get_friendlyPath($strFilePath);

              /*
               * Get the tooltip for this file.
               * a) desc
               * b) title
               * c) timestamp
               */
              $strTooltip = (StringHelper::hasText($oDataFile->getDescription())) ? $oDataFile->getDescription() : $oDataFile->getTitle();
              if(!StringHelper::hasText($strTooltip)){
                $strTooltip =  "Timestamp: ".$strFileTimestamp;
              }

              /*
               * Figure out if we have a jpg, png, or gif.
               * If so, link to the 800x600 file
               */
              $strFileNameParts = explode('.', $strFileName);
              $strFileExtension = array_pop($strFileNameParts);

              //validate extension
              $extOk = false;
              $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
              foreach($validFileExts as $key => $value){
                if( preg_match("/$value/i", $strFileExtension ) ){
                  $extOk = true;
                  break;
                }

              }

              $strFileFriendlyPath = $oDataFile->getFriendlyPath();
              $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();

              //determine if scaled image is available
              $strLightbox = StringHelper::EMPTY_STRING;
              if($extOk){
                $strDisplayName = "display_".$iDataFileId."_".$strFileName;
                $strDisplayPath = $strFilePath."/".Files::GENERATED_PICS;

                if(is_file($strDisplayPath."/".$strDisplayName)){
                  $oDataFile->setName($strDisplayName);
                  $oDataFile->setPath($strDisplayPath);
                  $strLightbox = "rel=lightbox[data] ";

                  $strFileFriendlyPath = $oDataFile->getFriendlyPath();
                  $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();
                }
              }

              //default the link to a file.  if an image, put in lightbox (png,jpg,gif)
              $strLinkToFileUrl = $oDataFile->getUrl();
              $strThisLink = <<< ENDHTML
                <a title="$strTooltip"
                   href="$strLinkToFileUrl" $strLightbox>
                  $strFileName
                </a>
                $strCurationStatus
ENDHTML;


              /*
               * If we're dealing with a directory,
               * get the number of files and sub-folders.
               * Also, link to the data view of the project
               * editor, not component com_data.
               */
              $strFileCount = "";
              $strDirectoryCount = "";
              $strDirectory = "";
              $iDirectorySize = ($oDataFile->isDirectory()) ? DataFilePeer::getDirectorySize($oDataFile->getFullPath()) : 0;
              $iFileSize = (!$oDataFile->isDirectory()) ? $oDataFile->get_friendlySize() : "";
              $strFileCreated = ($oDataFile->getCreated()) ? $oDataFile->getCreated() : "";
              $strIcon = self::getIcon($oDataFile);

              if( $oDataFile->getDirectory()==1){
                if($iDirectorySize > 0){
                  $iFileSize = cleanSize($iDirectorySize);

                  $strDirectory = <<< ENDHTML
                    <a style="border:0px;" title="$strTooltip"
                       href="/warehouse/filebrowser/$p_iProjectId?path=$strFileSystemPath&toppath=$p_strTopPath">
                      $strIcon
                    </a>
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <a title="$strTooltip"
                      href="/warehouse/filebrowser/$p_iProjectId?path=$strFileSystemPath&toppath=$p_strTopPath">
                      $strFileName
                    </a>
                    $strCurationStatus
ENDHTML;
                }else{
                  $strDirectory = <<< ENDHTML
                    $strIcon
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <span class="emptyFolder">
                      $strFileName (0 files)
                    </span>
                    $strCurationStatus
ENDHTML;
                }
              }

              //validate if data file on file system
              if($oDataFile->existsInFilesystem()){
                //exclude generated pics from the listing
                if($strFileName != Files::GENERATED_PICS && self::isViewable($oDataFile)){
                  $strBgColor = ($iIndex%2==0) ? "even" : "odd";
                  ++$iIndex;

                  $strThisIcon = ($strDirectory=="") ? $strIcon : $strDirectory;

                  $strReturn .= <<< ENDHTML
                    <tr class="$strBgColor">
                      <td><input id="$p_iProjectId" type="checkbox" name="cbxDataFile[]" value="$iDataFileId" onClick="computeDownloadSize(this, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                      <td>$strThisIcon</td>
                      <td>$strThisLink</td>
                      <td>$iFileSize</td>
                      <td><a href="/warehouse/filebrowser/$p_iProjectId?path=$strFilePath">$strDirPath</a></td>
                    </tr>
ENDHTML;
                }//end .Generated_Pics
              }//end file exists
            }//end foreach
          }else{
            $strReturn .= <<< ENDHTML
            <tr>
              <td colspan="4">
                No files found.
              </td>
            </tr>
ENDHTML;
          }

    $strDownloadDiv = self::getDownloadDiv($iMaxDownload, $strMaxDownload);

    $strReturn .= <<< ENDHTML
                </table>
                <br>
                $strDownloadDiv
ENDHTML;

    return $strReturn;

  }

  public function getFileBrowserByInDeed($p_strType, $p_oCurrentDataFileArray, $p_strTopPath, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0, $p_strCurrentPath="", $p_strToolReturn=""){
    $strExperimentFilter = self::getExperimentFilter($p_iProjectId, $p_iExperimentId);
    $strTrialFilter = self::getTrialFilter($p_iExperimentId, $p_iTrialId);
    $strRepetitionFilter = self::getRepetitionFilter($p_iTrialId, $p_iRepetitionId);

    $strReturn = <<< ENDHTML
                    <input type="hidden" name="path" value="$p_strCurrentPath"/>
                    <input type="hidden" name="type" value="$p_strType"/>
                    <div id="fileBrowserFilters" style="float:right">
                      <div class="editorInputFloat editorInputMargin">
                        Filter:
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strExperimentFilter
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strTrialFilter
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strRepetitionFilter
                      </div>
                      <div class="clearFloat"></div>
                    </div>
                    <div class="clearFloat"></div>
ENDHTML;

    $iMaxDownload = 500000000;
    $strMaxDownload = "500 MB";

    $strReturn .= <<< ENDHTML
              <table cellpadding="1" cellspacing="1" style="margin-top:15px;">
                <thead>
                  <th valign="top" colspan="6" style="white-space:normal">
                    File Type:&nbsp;&nbsp;
                    <span style="font-weight:normal;">$p_strType</span>
                  </th>
                </thead>

                <tr>
                  <td width="1"><input id="checkAll" type="checkbox" name="checkAll" onClick="setAllDownloadCheckBoxes('frmData', 'cbxDataFile[]', this.checked, $p_iProjectId, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                  <td width="1"></td>
                  <td><b>Name</b></td>
                  <td><b>Size</b></td>
                  <td><b>Path</b></td>
                </tr>
ENDHTML;

          if(!empty($p_oCurrentDataFileArray)){
            $iIndex = 0;

            /* @var $oDataFile DataFile */
            foreach($p_oCurrentDataFileArray as $oDataFile){
              $strCurationStatus = $oDataFile->getCurationStatus();
              if($strCurationStatus==ProjectEditor::CURATION_REQUEST){
                $strCurationStatus = "<span class='curateSubmitted'>*</span>";
              }else{
                $strCurationStatus = "";
              }

              $strFileTimestamp = $oDataFile->getCreated();
              $iDataFileId = $oDataFile->getId();
              $strFilePath = $oDataFile->getPath();
              $strFileName = $oDataFile->getName();
              $strDirPath = get_friendlyPath($strFilePath);

              /*
               * Get the tooltip for this file.
               * a) desc
               * b) title
               * c) timestamp
               */
//              $strTooltip = (StringHelper::hasText($oDataFile->getDescription())) ? $oDataFile->getDescription() : $oDataFile->getTitle();
//              if(!StringHelper::hasText($strTooltip)){
//                $strTooltip =  "Timestamp: ".$strFileTimestamp;
//              }
              $strTooltip =  "Click to launch ".$oDataFile->getOpeningTool();
              $strTooltip = (StringHelper::hasText($oDataFile->getDescription())) ? $strTooltip.".: ".$oDataFile->getDescription() : $strTooltip;

              /*
               * Figure out if we have a jpg, png, or gif.
               * If so, link to the 800x600 file
               */

              /*
              $strFileNameParts = explode('.', $strFileName);
              $strFileExtension = array_pop($strFileNameParts);

              //validate extension
              $extOk = false;
              $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
              foreach($validFileExts as $key => $value){
                if( preg_match("/$value/i", $strFileExtension ) ){
                  $extOk = true;
                }
              }

              //determine if scaled image is available
              $strLightbox = StringHelper::EMPTY_STRING;
              if($extOk){
                $strDisplayName = "display_".$iDataFileId."_".$strFileName;
                $oDataFile->setName($strDisplayName);

                $strDisplayPath = $strFilePath."/".Files::GENERATED_PICS;
                $oDataFile->setPath($strDisplayPath);

                $strLightbox = "rel=lightbox[data] ";
              }
              */

              $strFileFriendlyPath = $oDataFile->getFriendlyPath();
              $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();

              $strLaunchInEED = NeesConfig::LAUNCH_INDEED;

              //default the link to launch inDEED
              $strThisLink = <<< ENDHTML
                <a href="$strLaunchInEED=$strFileSystemPath&$p_strToolReturn" title="$strTooltip">$strFileName</a>
                $strCurationStatus
ENDHTML;


              /*
               * If we're dealing with a directory,
               * get the number of files and sub-folders.
               * Also, link to the data view of the project
               * editor, not component com_data.
               */
              $strFileCount = "";
              $strDirectoryCount = "";
              $strDirectory = "";
              $iDirectorySize = ($oDataFile->isDirectory()) ? DataFilePeer::getDirectorySize($oDataFile->getFullPath()) : 0;
              $iFileSize = (!$oDataFile->isDirectory()) ? $oDataFile->get_friendlySize() : "";
              $strFileCreated = ($oDataFile->getCreated()) ? $oDataFile->getCreated() : "";
              $strIcon = self::getIcon($oDataFile);

              if( $oDataFile->getDirectory()==1){
                if($iDirectorySize > 0){
                  $iFileSize = cleanSize($iDirectorySize);

                  $strDirectory = <<< ENDHTML
                    <a style="border:0px;" title="$strTooltip"
                       href="/warehouse/filebrowser/$p_iProjectId?path=$strFileSystemPath&toppath=$p_strTopPath">
                      $strIcon
                    </a>
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <a title="$strTooltip"
                      href="/warehouse/filebrowser/$p_iProjectId?path=$strFileSystemPath&toppath=$p_strTopPath">
                      $strFileName
                    </a>
                    $strCurationStatus
ENDHTML;
                }else{
                  $strDirectory = <<< ENDHTML
                    $strIcon
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <span class="emptyFolder">
                      $strFileName (0 files)
                    </span>
                    $strCurationStatus
ENDHTML;
                }
              }

              //validate if data file on file system
              if($oDataFile->existsInFilesystem()){
                //exclude generated pics from the listing
                if($strFileName != Files::GENERATED_PICS && self::isViewable($oDataFile)){
                  $strBgColor = ($iIndex%2==0) ? "even" : "odd";
                  ++$iIndex;

                  $strThisIcon = ($strDirectory=="") ? $strIcon : $strDirectory;

                  $strReturn .= <<< ENDHTML
                    <tr class="$strBgColor">
                      <td><input id="$p_iProjectId" type="checkbox" name="cbxDataFile[]" value="$iDataFileId" onClick="computeDownloadSize(this, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                      <td>$strThisIcon</td>
                      <td>$strThisLink</td>
                      <td>$iFileSize</td>
                      <td><a href="/warehouse/filebrowser/$p_iProjectId?path=$strFilePath">$strDirPath</a></td>
                    </tr>
ENDHTML;
                }//end .Generated_Pics
              }//end file exists
            }//end foreach
          }else{
            $strReturn .= <<< ENDHTML
            <tr>
              <td colspan="4">
                No files found.
              </td>
            </tr>
ENDHTML;
          }

    $strDownloadDiv = self::getDownloadDiv($iMaxDownload, $strMaxDownload);

    $strReturn .= <<< ENDHTML
                </table>
                <br>
                $strDownloadDiv
ENDHTML;

    return $strReturn;

  }

  public function getFileBrowserBySearch($p_strTerm, $p_oCurrentDataFileArray, $p_strTopPath, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0, $p_strCurrentPath=""){
    $strExperimentFilter = self::getExperimentFilter($p_iProjectId, $p_iExperimentId);
    $strTrialFilter = self::getTrialFilter($p_iExperimentId, $p_iTrialId);
    $strRepetitionFilter = self::getRepetitionFilter($p_iTrialId, $p_iRepetitionId);

    $strReturn = <<< ENDHTML
                    <input type="hidden" name="path" value="$p_strCurrentPath"/>
                    <input type="hidden" name="type" value="Search"/>
                    <input type="hidden" name="term" value="$p_strTerm"/>
                    <div id="fileBrowserFilters" style="float:right">
                      <div class="editorInputFloat editorInputMargin">
                        Filter:
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strExperimentFilter
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strTrialFilter
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strRepetitionFilter
                      </div>
                      <div class="clearFloat"></div>
                    </div>
                    <div class="clearFloat"></div>
ENDHTML;

    $iMaxDownload = 500000000;
    $strMaxDownload = "500 MB";

    $strReturn .= <<< ENDHTML
              <table cellpadding="1" cellspacing="1" style="margin-top:15px;">
                <thead>
                  <th valign="top" colspan="6" style="white-space:normal">
                    Search:&nbsp;&nbsp;
                    <span style="font-weight:normal;">$p_strTerm</span>
                  </th>
                </thead>

                <tr>
                  <td width="1"><input id="checkAll" type="checkbox" name="checkAll" onClick="setAllDownloadCheckBoxes('frmData', 'dataFile[]', this.checked, $p_iProjectId, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                  <td width="1"></td>
                  <td><b>Name</b></td>
                  <td><b>Size</b></td>
                  <td><b>Path</b></td>
                </tr>
ENDHTML;

          if(!empty($p_oCurrentDataFileArray)){
            $iIndex = 0;

            /* @var $oDataFile DataFile */
            foreach($p_oCurrentDataFileArray as $oDataFile){
              $strCurationStatus = $oDataFile->getCurationStatus();
              if($strCurationStatus==ProjectEditor::CURATION_REQUEST){
                $strCurationStatus = "<span class='curateSubmitted'>*</span>";
              }else{
                $strCurationStatus = "";
              }

              $strFileTimestamp = $oDataFile->getCreated();
              $iDataFileId = $oDataFile->getId();
              $strFilePath = $oDataFile->getPath();
              $strFileName = $oDataFile->getName();
              $strDirPath = get_friendlyPath($strFilePath);

              /*
               * Get the tooltip for this file.
               * a) desc
               * b) title
               * c) timestamp
               */
              $strTooltip = (StringHelper::hasText($oDataFile->getDescription())) ? $oDataFile->getDescription() : $oDataFile->getTitle();
              if(!StringHelper::hasText($strTooltip)){
                $strTooltip =  "Timestamp: ".$strFileTimestamp;
              }

              /*
               * Figure out if we have a jpg, png, or gif.
               * If so, link to the 800x600 file
               */
              $strFileNameParts = explode('.', $strFileName);
              $strFileExtension = array_pop($strFileNameParts);

              //validate extension
              $extOk = false;
              $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
              foreach($validFileExts as $key => $value){
                if( preg_match("/$value/i", $strFileExtension ) ){
                  $extOk = true;
                  break;
                }

              }

              $strFileFriendlyPath = $oDataFile->getFriendlyPath();
              $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();

              //determine if scaled image is available
              $strLightbox = StringHelper::EMPTY_STRING;
              if($extOk){
                $strDisplayName = "display_".$iDataFileId."_".$strFileName;
                $strDisplayPath = $strFilePath."/".Files::GENERATED_PICS;

                if(is_file($strDisplayPath."/".$strDisplayName)){
                  $oDataFile->setName($strDisplayName);
                  $oDataFile->setPath($strDisplayPath);
                  $strLightbox = "rel=lightbox[data] ";

                  $strFileFriendlyPath = $oDataFile->getFriendlyPath();
                  $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();
                }
              }

              //default the link to a file.  if an image, put in lightbox (png,jpg,gif)
              $strLinkToFileUrl = $oDataFile->getUrl();
              $strThisLink = <<< ENDHTML
                <a title="$strTooltip"
                   href="$strLinkToFileUrl" $strLightbox>
                  $strFileName
                </a>
                $strCurationStatus
ENDHTML;


              /*
               * If we're dealing with a directory,
               * get the number of files and sub-folders.
               * Also, link to the data view of the project
               * editor, not component com_data.
               */
              $strFileCount = "";
              $strDirectoryCount = "";
              $strDirectory = "";
              $iDirectorySize = ($oDataFile->isDirectory()) ? DataFilePeer::getDirectorySize($oDataFile->getFullPath()) : 0;
              $iFileSize = (!$oDataFile->isDirectory()) ? $oDataFile->get_friendlySize() : "";
              $strFileCreated = ($oDataFile->getCreated()) ? $oDataFile->getCreated() : "";
              $strIcon = self::getIcon($oDataFile);

              if( $oDataFile->getDirectory()==1){
                if($iDirectorySize > 0){
                  $iFileSize = cleanSize($iDirectorySize);

                  $strDirectory = <<< ENDHTML
                    <a style="border:0px;" title="$strTooltip"
                       href="/warehouse/filebrowser/$p_iProjectId?path=$strFileSystemPath&toppath=$p_strTopPath">
                      $strIcon
                    </a>
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <a title="$strTooltip"
                      href="/warehouse/filebrowser/$p_iProjectId?path=$strFileSystemPath&toppath=$p_strTopPath">
                      $strFileName
                    </a>
                    $strCurationStatus
ENDHTML;
                }else{
                  $strDirectory = <<< ENDHTML
                    $strIcon
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <span class="emptyFolder">
                      $strFileName (0 files)
                    </span>
                    $strCurationStatus
ENDHTML;
                }
              }

              //validate if data file on file system
              if($oDataFile->existsInFilesystem()){
                //exclude generated pics from the listing
                if($strFileName != Files::GENERATED_PICS && self::isViewable($oDataFile)){
                  $strBgColor = ($iIndex%2==0) ? "even" : "odd";
                  ++$iIndex;

                  $strThisIcon = ($strDirectory=="") ? $strIcon : $strDirectory;

                  $strReturn .= <<< ENDHTML
                    <tr class="$strBgColor">
                      <td><input id="$p_iProjectId" type="checkbox" name="cbxDataFile[]" value="$iDataFileId" onClick="computeDownloadSize(this, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                      <td>$strThisIcon</td>
                      <td>$strThisLink</td>
                      <td>$iFileSize</td>
                      <td><a href="/warehouse/filebrowser/$p_iProjectId?path=$strFilePath">$strDirPath</a></td>
                    </tr>
ENDHTML;
                }//end .Generated_Pics
              }
            }//end foreach
          }else{
            $strReturn .= <<< ENDHTML
            <tr>
              <td colspan="4">
                No files found.
              </td>
            </tr>
ENDHTML;
          }

    $strDownloadDiv = self::getDownloadDiv($iMaxDownload, $strMaxDownload);

    $strReturn .= <<< ENDHTML
                </table>
                <br>
                $strDownloadDiv
ENDHTML;

    return $strReturn;

  }

  public function getFileBrowserByMedia($p_strType, $p_oCurrentDataFileArray, $p_strTopPath, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0, $p_strCurrentPath=""){
    $strExperimentFilter = self::getExperimentFilter($p_iProjectId, $p_iExperimentId);
    $strTrialFilter = self::getTrialFilter($p_iExperimentId, $p_iTrialId);
    $strRepetitionFilter = self::getRepetitionFilter($p_iTrialId, $p_iRepetitionId);

    $strReturn = <<< ENDHTML
                    <input type="hidden" name="path" value="$p_strCurrentPath"/>
                    <input type="hidden" name="type" value="$p_strType"/>
                    <div id="fileBrowserFilters" style="float:right">
                      <div class="editorInputFloat editorInputMargin">
                        Filter:
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strExperimentFilter
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strTrialFilter
                      </div>
                      <div class="editorInputFloat editorInputMargin">
                        $strRepetitionFilter
                      </div>
                      <div class="clearFloat"></div>
                    </div>
                    <div class="clearFloat"></div>
ENDHTML;

    $iMaxDownload = 500000000;
    $strMaxDownload = "500 MB";

    $strReturn .= <<< ENDHTML
              <table cellpadding="1" cellspacing="1" style="margin-top:15px;">
                <thead>
                  <th valign="top" colspan="6" style="white-space:normal">
                    File Type:&nbsp;&nbsp;
                    <span style="font-weight:normal;">$p_strType</span>
                  </th>
                </thead>
                <tr>
ENDHTML;

          if(!empty($p_oCurrentDataFileArray)){
            $iIndex = 0;

            /* @var $oDataFile DataFile */
            foreach($p_oCurrentDataFileArray as $oDataFile){
              $strFileTimestamp = $oDataFile->getCreated();
              $iDataFileId = $oDataFile->getId();
              $strFilePath = $oDataFile->getPath();         //original path
              $strFileName = $oDataFile->getName();         //original name
              $strDirPath = get_friendlyPath($strFilePath); //friendly path
              $iFileSize = (!$oDataFile->isDirectory()) ? $oDataFile->get_friendlySize() : "";

              /*
               * Figure out if we have a jpg, png, or gif.
               * If so, link to the 800x600 file
               */
              $strFileNameParts = explode('.', $strFileName);
              $strFileExtension = array_pop($strFileNameParts);

              //validate extension
              $extOk = false;
              $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
              foreach($validFileExts as $key => $value){
                if( preg_match("/$value/i", $strFileExtension ) ){
                  $extOk = true;
                }
              }

              //determine if scaled image is available
              $strLightbox = StringHelper::EMPTY_STRING;
              if($extOk){
                $strLightbox = "rel=lightbox[data] ";
              }

              $strDisplayPath = $strFilePath."/".Files::GENERATED_PICS;
              $oDataFile->setPath($strDisplayPath);

              $strDisplayName = "display_".$iDataFileId."_".$strFileName;
              $oDataFile->setName($strDisplayName);
              $strLinkToFileUrl = $oDataFile->getUrl();

              $strFileFriendlyPath = $oDataFile->getFriendlyPath();
              $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();

              $strThumbName = "thumb_".$iDataFileId."_".$strFileName;
              $oDataFile->setName($strThumbName);
              $strThumbUrl = $oDataFile->get_url();

              $strTooltip = (StringHelper::hasText($oDataFile->getDescription())) ? $oDataFile->getDescription() : $oDataFile->getTitle();
              if(!StringHelper::hasText($strTooltip)){
                $strTooltip =  $strDirPath."/".$strFileName;
              }else{
                $strTooltip .=  " :: ".$strDirPath."/".$strFileName;
              }

              //default the link to a file.  if an image, put in lightbox (png,jpg,gif)
              
              $strThisLink = <<< ENDHTML
                <a title="$strTooltip" alt="$strFileFriendlyPath"
                   href="$strLinkToFileUrl" $strLightbox>
                  <img src="$strThumbUrl" border="0"/>
                </a><br>
                File Size: $iFileSize<br>
                <input id="$p_iProjectId" type="checkbox" name="cbxDataFile[]" value="$iDataFileId"
                    onClick="computeDownloadSize(this, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/> Download
ENDHTML;

              //validate if data file on file system
              if($oDataFile->existsInFilesystem()){
                //exclude generated pics from the listing
                if($strFileName != Files::GENERATED_PICS && self::isViewable($oDataFile)){
                  $strBgColor = ($iIndex%2==0) ? "even" : "odd";
                  $iPhotoCounter = $iIndex + 1;

                  $strReturn .= <<< ENDHTML
                      <td align="center" style="padding-bottom:30px;">$strThisLink</td>
ENDHTML;
                  if($iIndex>0 && $iPhotoCounter%5===0){
                    $strReturn .= "</tr>";
                    if($iIndex < sizeof($p_oCurrentDataFileArray)){
                      $strReturn .= "<tr>";
                    }
                  }

                  ++$iIndex;
                }//end .Generated_Pics
              }//end file exists
            }//end foreach
          }else{
            $strReturn .= <<< ENDHTML
              <td colspan="4">No files found.</td>
ENDHTML;
          }

    $strDownloadDiv = self::getDownloadDiv($iMaxDownload, $strMaxDownload);

    $strReturn .= <<< ENDHTML
                </tr>
                </table>
                <br>
                $strDownloadDiv
ENDHTML;

    return $strReturn;

  }

  public function getFileBrowserByAjax($p_strCurrentPath, $p_oCurrentDataFileArray, $p_iTopDirectoryIndex, $p_iProjectId, $p_strForm, $p_strTarget, $p_strToolReturnUrl=""){
    $strReturn = "";

    //create a friendly looking path
    $strCurrentFriendlyPath = get_friendlyPath($p_strCurrentPath);

    //provide breadcrumbs for file browser
    $strLocationPath = "";
    $strLocationArray = explode("/", $strCurrentFriendlyPath);
    array_shift($strLocationArray);

    $strLocationLinks = "";
    foreach($strLocationArray as $iLinkIndex=>$strLocation){
      $strLocationLinks .= "/".$strLocation;
      $strLocationLinks = get_systemPath($strLocationLinks);

      //lock users down to specific directories.
      if($iLinkIndex > $p_iTopDirectoryIndex){
        $strLocationPath = $strLocationPath .<<< ENDHTML
         / <a href="javascript:void(0);" onClick="getMootools('/warehouse/data?path=$strLocationLinks&type=Ajax&format=ajax&form=$p_strForm&target=$p_strTarget', '$p_strTarget');">
            $strLocation
           </a>
ENDHTML;
      }else{
        $strLocationPath = $strLocationPath .<<< ENDHTML
       / $strLocation
ENDHTML;
      }
    }

    $iMaxDownload = 500000000;
    $strMaxDownload = "500 MB";

    $strReturn .= <<< ENDHTML
              <div  id="projectDocs$p_strTarget" style="margin-top:15px;">
              <table cellpadding="1" cellspacing="1">
                <thead>
                  <th valign="top" colspan="6" style="white-space:normal">
                    Location:&nbsp;&nbsp;
                    <span style="font-weight:normal;">$strLocationPath</span>
                  </th>
                </thead>

                <tr>
                  <td width="1"><input id="checkAll" type="checkbox" name="checkAll" onClick="setAllDownloadCheckBoxes('$p_strForm', 'cbxDataFile[]', this.checked, $p_iProjectId, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                  <td width="1"></td>
                  <td><b>Name</b></td>
                  <td><b>Size</b></td>
                  <td><b>Timestamp</b></td>
                  <td><b>Application</b></td>
                </tr>
ENDHTML;

          $iIndex = 0;
          if(!empty($p_oCurrentDataFileArray)){


            /* @var $oDataFile DataFile */
            foreach($p_oCurrentDataFileArray as $oDataFile){
              $strCurationStatus = $oDataFile->getCurationStatus();
              if($strCurationStatus==ProjectEditor::CURATION_REQUEST){
                $strCurationStatus = "<span class='curateSubmitted'>*</span>";
              }else{
                $strCurationStatus = "";
              }

              $strFileTimestamp = $oDataFile->getCreated();
              $iDataFileId = $oDataFile->getId();
              $strFilePath = $oDataFile->getPath();
              $strFileName = $oDataFile->getName();
              $strDataFileLinkTitle = "";

              /* @var $oDataFileLink DataFileLink */
              if(preg_match("/Experiment-([0-9])+/", $strFileName)){
                $oDataFileLink = DataFileLinkPeer::retrieveByPK($iDataFileId);
                $strDataFileLinkTitle = StringHelper::neat_trim($oDataFileLink->getExperiment()->getTitle(), 50);
              }

              if(preg_match("/Trial-([0-9])+/", $strFileName)){
                $oDataFileLink = DataFileLinkPeer::retrieveByPK($iDataFileId);
                $strDataFileLinkTitle = StringHelper::neat_trim($oDataFileLink->getTrial()->getTitle(), 50);
              }

              /*
               * Get the tooltip for this file.
               * a) desc
               * b) title
               * c) timestamp
               */
              $strTooltip = (StringHelper::hasText($oDataFile->getDescription())) ? $oDataFile->getDescription() : $oDataFile->getTitle();
              if(!StringHelper::hasText($strTooltip)){
                $strTooltip =  "Timestamp: ".$strFileTimestamp;
              }

              /*
               * Figure out if we have a jpg, png, or gif.
               * If so, link to the 800x600 file
               */
              $strFileNameParts = explode('.', $strFileName);
              $strFileExtension = array_pop($strFileNameParts);

              //validate extension
              $extOk = false;
              $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
              foreach($validFileExts as $key => $value){
                if( preg_match("/$value/i", $strFileExtension ) ){
                  $extOk = true;
                  break;
                }

              }

              $strFileFriendlyPath = $oDataFile->getFriendlyPath();
              $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();

              //determine if scaled image is available
              $strLightbox = StringHelper::EMPTY_STRING;
              if($extOk){
                $strDisplayName = "display_".$iDataFileId."_".$strFileName;
                $strDisplayPath = $strFilePath."/".Files::GENERATED_PICS;

                if(is_file($strDisplayPath."/".$strDisplayName)){
                  $oDataFile->setName($strDisplayName);
                  $oDataFile->setPath($strDisplayPath);
                  //$strLightbox = "rel=lightbox[data] ";
                  $strLightbox = "target='neesPhoto' ";

                  $strFileFriendlyPath = $oDataFile->getFriendlyPath();
                  $strFileSystemPath = $oDataFile->getPath() ."/". $oDataFile->getName();
                }
              }

              //default the link to a file.  if an image, put in lightbox (png,jpg,gif)
              $strLinkToFileUrl = $oDataFile->getUrl();
              $strThisLink = <<< ENDHTML
                <a title="$strTooltip"
                   href="$strLinkToFileUrl" $strLightbox>
                  $strFileName
                </a>
                $strCurationStatus
ENDHTML;


              /*
               * If we're dealing with a directory,
               * get the number of files and sub-folders.
               * Also, link to the data view of the project
               * editor, not component com_data.
               */
              $strFileCount = "";
              $strDirectoryCount = "";
              $strDirectory = "";
              $iDirectorySize = ($oDataFile->isDirectory()) ? DataFilePeer::getDirectorySize($oDataFile->getFullPath()) : 0;
              $iFileSize = (!$oDataFile->isDirectory()) ? $oDataFile->get_friendlySize() : "";
              $strFileCreated = ($oDataFile->getCreated()) ? $oDataFile->getCreated() : "";
              $strTool = self::getTool($oDataFile, $p_strToolReturnUrl);
              $strIcon = self::getIcon($oDataFile);

              if( $oDataFile->getDirectory()==1){
                if(StringHelper::hasText($strDataFileLinkTitle)){
                  $strDataFileLinkTitle = ": ".$strDataFileLinkTitle;
                }

                if($iDirectorySize > 0){
                  $iFileSize = cleanSize($iDirectorySize);

                  $strDirectory = <<< ENDHTML
                    <a style="border:0px;" title="$strTooltip"
                       href="javascript:void(0);" onClick="getMootools('/warehouse/data?path=$strFileSystemPath&type=Ajax&format=ajax&top=$p_iTopDirectoryIndex&form=$p_strForm&target=$p_strTarget', '$p_strTarget');">
                      $strIcon
                    </a>
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <a style="border:0px;" title="$strTooltip"
                       href="javascript:void(0);" onClick="getMootools('/warehouse/data?path=$strFileSystemPath&type=Ajax&format=ajax&top=$p_iTopDirectoryIndex&form=$p_strForm&target=$p_strTarget', '$p_strTarget');">
                      $strFileName $strDataFileLinkTitle
                    </a>
                    $strCurationStatus
ENDHTML;
                }else{
                  $strDirectory = <<< ENDHTML
                    $strIcon
                    $strCurationStatus
ENDHTML;

                  $strThisLink = <<< ENDHTML
                    <span class="emptyFolder">
                      $strFileName $strDataFileLinkTitle (0 files)
                    </span>
                    $strCurationStatus
ENDHTML;
                }
              }

              //validate if data file on file system
              if($oDataFile->existsInFilesystem()){
                //exclude generated pics from the listing
                if($strFileName != Files::GENERATED_PICS && self::isViewable($oDataFile)){
                  $strBgColor = ($iIndex%2==0) ? "even" : "odd";
                  ++$iIndex;

                  $strThisIcon = ($strDirectory=="") ? $strIcon : $strDirectory;


                  $strLaunchInEED = NeesConfig::LAUNCH_INDEED;

                  $strReturn .= <<< ENDHTML
                    <tr class="$strBgColor">
                      <td><input id="$p_iProjectId" type="checkbox" name="cbxDataFile[]" value="$iDataFileId" onClick="computeDownloadSize(this, 'downloadSum', $iMaxDownload, '$strMaxDownload', '/warehouse/downloadsize?format=ajax', 'approxDownloadSize');"/></td>
                      <td>$strThisIcon</td>
                      <td>$strThisLink</td>
                      <td>$iFileSize</td>
                      <td>$strFileCreated</td>
                      <td>$strTool</td>
                    </tr>
ENDHTML;
                }
                //end .Generated_Pics
              }
            }//end foreach
          }else{
            $strReturn .= <<< ENDHTML
            <tr>
              <td colspan="4">
                No files found.
              </td>
            </tr>
ENDHTML;
          }

    $strDownloadDiv = self::getDownloadDiv($iMaxDownload, $strMaxDownload, $p_strForm);

    $strReturn .= <<< ENDHTML
                </table>
                <br>
                $strDownloadDiv
                </div>
ENDHTML;

    return $strReturn;

  }

  public function getDownloadDiv($p_iMaxDownloadSize, $p_strMaxDownloadSize, $p_strForm="frmData"){
    $strReturn = <<< ENDHTML
                <div id="downloads">
                  <input type="hidden" id="downloadSum" name="downloadSum" value="0"/>
                  <div id="downloadButton" class="warehouseFloat warehouseMargin"><input type="button" value="Download" onClick="downloadFileBrowser('$p_strForm', 'cbxDataFile[]', '/warehouse/download', 'downloadSum', $p_iMaxDownloadSize);"/></div>
                  <div id="approxDownloadSize" class="warehouseFloat">Approximate Download File: 0 b (max is $p_strMaxDownloadSize)</div>
                  <div class="clearFloat"></div>
                </div>
ENDHTML;
    return $strReturn;
  }

  public function findByTitle($p_strTitle, $p_iLowerLimit=1, $p_iUpperLimit=10, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findByTitle($p_strTitle, $p_iLowerLimit, $p_iUpperLimit, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  public function findByTitleCount($p_strTitle, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findByTitleCount($p_strTitle, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  public function findByName($p_strName, $p_iLowerLimit=1, $p_iUpperLimit=10){
    return DataFilePeer::findByName($p_strName, $p_iLowerLimit, $p_iUpperLimit);
  }

  public function findByNameCount($p_strName) {
    return DataFilePeer::findByNameCount($p_strName);
  }

  /**
   *
   * @param DataFile $p_oDataFile
   * @return string
   */
  public function getIcon($p_oDataFile){
    $strReturn = "";

    $strMimeType = "";
    try{
      if($p_oDataFile->getDocumentFormat() != null){
        $strMimeType = $p_oDataFile->getDocumentFormat()->getMimeType();
      }
    }catch(Exception $oExp){

    }

    try{
      if($p_oDataFile->isDirectory()){
        $strReturn = "<img src='/components/com_warehouse/images/icons/folder.gif' border='0'/>";
      }elseif($p_oDataFile->getUsageTypeId()>=200 && $p_oDataFile->getUsageTypeId()<=203){
        $strReturn = "<img src='/templates/fresh/images/icons/fc-compass.gif' border='0'/>";
      }elseif(StringHelper::hasString (strtolower($strMimeType), "image")){
        $strReturn = "<img src='/templates/fresh/images/icons/camera.png' border='0'/>";
      }elseif(StringHelper::hasString (strtolower($strMimeType), "video")){
        $strReturn = "<img src='/templates/fresh/images/icons/fc-animation.gif' border='0'/>";
      }elseif(self::isCuratedByTypeAndNCId($p_oDataFile->getId(), "Report")){
        $strReturn = "<img src='/templates/fresh/images/icons/fc-series.gif' border='0'/>";
      }elseif(self::isCuratedByTypeAndNCId($p_oDataFile->getId(), "Presentation")){
        $strReturn = "<img src='/templates/fresh/images/icons/fc-presentation.gif' border='0'/>";
      }elseif(self::isCuratedByTypeAndNCId($p_oDataFile->getId(), "Publication")){
        $strReturn = "<img src='/templates/fresh/images/icons/fc-publication.gif' border='0'/>";
      }elseif(self::isCuratedByTypeAndNCId($p_oDataFile->getId(), "Chart")){
        $strReturn = "<img src='/templates/fresh/images/icons/chart_bar.png' border='0'/>";
      }elseif($p_oDataFile->getOpeningTool()=="inDEED"){
        $strReturn = "<img src='/templates/fresh/images/icons/fc-tool.gif' border='0'/>";
      }
    }catch(Exception $oException){
        //var_dump($oException);
    }
    return $strReturn;
  }

  public function isCuratedByTypeAndNCId($p_iNeesCentralId, $p_strObjectType){
    return DataFilePeer::isCuratedByTypeAndNCId($p_iNeesCentralId, $p_strObjectType);
  }

  /**
   *
   * @param DataFile $p_oDataFile
   */
  public function getTool($p_oDataFile, $p_strToolReturnUrl=""){
    $strTool = $p_oDataFile->getOpeningTool();
    if(!StringHelper::hasText($strTool)){
      return StringHelper::EMPTY_STRING;
    }

    if($strTool == "inDEED"){
      $strIndeedName = $p_oDataFile->getName();
      $strIndeedPath = $p_oDataFile->getPath();
      $strDescription = $p_oDataFile->getDescription();

      $strLaunchInEED = NeesConfig::LAUNCH_INDEED;

      $strTooltip = (StringHelper::hasText($strDescription)) ? "Click to launch inDEED: ".$strDescription : "Click to launch inDEED.";

      $strTool = "<a href='$strLaunchInEED=$strIndeedPath/$strIndeedName&$p_strToolReturnUrl' title='$strTooltip'>$strTool</a>";
    }
    return $strTool;
  }

  /**
   *
   * @param int $p_iProjectId
   * @return string
   */
  public function getExperimentFilter($p_iProjectId, $p_iExperimentId=0){
    $oAuthorizer = Authorizer::getInstance();

    /* @var $oProject Project */
    $oProject = ProjectPeer::find($p_iProjectId);

    $strReturn = <<< ENDHTML
                    <!--<input type="hidden" id="txtExperiment" name="experiment" value="$p_iExperimentId"/>-->
                    <select id="cboExperiment" name="experimentId" onChange="onChangeFileBrowser('frmData', 'cboExperiment', 'cboTrial', 'cboRepetition');">
                      <option value="0">-Select Experiment-</option>
ENDHTML;

    $oExperimentArray = $oProject->getExperiments();
    foreach($oExperimentArray as $oExperiment){
      /*var $oExperiment Experiment*/
      if($oAuthorizer->canView($oExperiment)){
        $iExperimentId = $oExperiment->getId();
        $strExperimentName = $oExperiment->getName();
        $strExperimentTitle = StringHelper::neat_trim($oExperiment->getTitle(), 50);
        $strSelected = ($iExperimentId==$p_iExperimentId) ? "selected" : "";
        $strReturn .= <<< ENDHTML
                      <option value="$iExperimentId" $strSelected>$strExperimentName: $strExperimentTitle</option>
ENDHTML;
      }
    }

    $strReturn .= <<< ENDHTML
                    </select>
ENDHTML;

    return $strReturn;
  }

  public function getTrialFilter($p_iExperimentId=0, $p_iTrialId=0){
    $strReturn = <<< ENDHTML
                    <!--<input type="hidden" id="txtTrial" name="trialId" value="$p_iTrialId"/>-->
                    <select id="cboTrial" name="trialId" onChange="onChangeFileBrowser('frmData', 'cboExperiment', 'cboTrial', 'cboRepetition');">
                      <option value="0">-Select Trial-</option>

ENDHTML;

    /*var $oExperiment Experiment*/
    $oExperiment = ExperimentPeer::retrieveByPK($p_iExperimentId);
    if($oExperiment){
      $oTrialArray = $oExperiment->getTrials();
      foreach($oTrialArray as $oTrial){
        /* @var $oTrial Trial */
        $strTrialName = $oTrial->getName();
        $strTrialTitle = StringHelper::neat_trim($oTrial->getTitle(), 50);
        $strDisplay = (StringHelper::hasText($strTrialTitle)) ? $strTrialName.": ". $strTrialTitle : $strTrialName;
        $iTrialId = $oTrial->getId();
        $strSelected = ($iTrialId==$p_iTrialId) ? "selected" : "";

        $strReturn .= <<< ENDHTML
          <option value="$iTrialId" $strSelected>$strDisplay</option>
ENDHTML;
      }
    }

    $strReturn .= <<< ENDHTML
                    </select>
ENDHTML;
    return $strReturn;
  }

  public function getRepetitionFilter($p_iTrialId=0, $p_iRepetitionId=0){
    $strReturn = <<< ENDHTML
                    <!--<input type="hidden" id="txtRepetition" name="repetition" value="$p_iRepetitionId"/>-->
                    <select id="cboRepetition" name="repetitionId" onChange="onChangeFileBrowser('frmData', 'cboExperiment', 'cboTrial', 'cboRepetition');">
                      <option value="0">-Select Repetition-</option>
ENDHTML;

    /*var $oTrial Trial*/
    $oTrial = TrialPeer::retrieveByPK($p_iTrialId);
    if($oTrial){
      $oRepetitionArray = $oTrial->getRepetitions();
      foreach($oRepetitionArray as $oRepetition){
        /* @var $oRepetition Repetition */
        $strRepetitionName = $oRepetition->getName();
        $iRepetitionId = $oRepetition->getId();
        $strSelected = ($iRepetitionId==$p_iRepetitionId) ? "selected" : "";

        $strReturn .= <<< ENDHTML
          <option value="$iRepetitionId" $strSelected>$strRepetitionName</option>
ENDHTML;
      }
    }

    $strReturn .= <<< ENDHTML
                    </select>
ENDHTML;
    return $strReturn;
  }
}
