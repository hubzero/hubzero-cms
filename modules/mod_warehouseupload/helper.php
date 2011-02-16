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

require_once 'lib/data/EntityTypePeer.php';
require_once 'lib/data/EntityType.php';
require_once 'lib/data/DataFile.php';
require_once 'api/org/nees/lib/interface/Data.php';
require_once 'api/org/nees/static/Files.php';
require_once 'api/org/nees/static/ProjectEditor.php';

class modWarehouseUploadHelper{
	
  public function uploadDrawing($p_oUsageEntityTypeArray){
    $strReturn = "";

    $strEntityTypes = "<select id='cboUsage' name='usageType'>";
    foreach($p_oUsageEntityTypeArray as $oEntityType){
      /* @var $oEntityType EntityType */
      $iEntityTypeId = $oEntityType->getId();
      $strEntityName = $oEntityType->getDatabaseTableName();

      $strEntityTypes .= <<< ENDHTML
       <option value="$iEntityTypeId">$strEntityName</option>
ENDHTML;
    }
    $strEntityTypes .= "</select>";

    $strReturn .= <<< ENDHTML
      <fieldset style="width: 100%;">
        <fieldset>
          <legend>Upload New Drawing File</legend>
          <div id="">
            <table style="border:0px;margin-top:15px;">
              <tr>
                <td width="1" nowrap="">File:</td>
                <td><input type="file" name="drawingFile"/></td>
              </tr>
              <tr>
                <td width="1" nowrap="">Type:</td>
                <td>$strEntityTypes</td>
              </tr>
              <tr>
                <td width="1" nowrap="">Title:</td>
                <td></td>
              </tr>
              <tr>
                <td width="1" nowrap="">Description:</td>
                <td><textarea onfocus="style.color='#000000'; this.value='';" style="width: 100%; height: 100px;" name="drawingDesc" id="taDrawingDesc"></textarea></td>
              </tr>
            </table>
          </div>
        </fieldset>
      </fieldset>
ENDHTML;

    return $strReturn;
  }
  
  public function uploadData(){
      
  }
  
  public function uploadImage(){
    $strReturn = "";

    $strEntityTypes = "<select id='cboUsage' name='usageType'>";
    foreach($p_oUsageEntityTypeArray as $oEntityType){
      /* @var $oEntityType EntityType */
      $iEntityTypeId = $oEntityType->getId();
      $strEntityName = $oEntityType->getDatabaseTableName();

      $strEntityTypes .= <<< ENDHTML
       <option value="$iEntityTypeId">$strEntityName</option>
ENDHTML;
    }
    $strEntityTypes .= "</select>";

    $strReturn .= <<< ENDHTML
      <fieldset style="width: 100%;">
        <fieldset>
          <legend>Upload New Image File</legend>
          <table style="border:0px;margin-top:15px;">
            <tr>
              <td width="1" nowrap="">File:</td>
              <td><input type="file" name="imageFile"/></td>
            </tr>
            <tr>
              <td width="1" nowrap="">Type:</td>
              <td>$strEntityTypes</td>
            </tr>
            <tr>
              <td width="1" nowrap="">Title:</td>
              <td></td>
            </tr>
            <tr>
              <td width="1" nowrap="">Description:</td>
              <td><textarea onfocus="style.color='#000000'; this.value='';" style="width: 100%; height: 100px;" name="drawingDesc" id="taDrawingDesc"></textarea></td>
            </tr>
          </table>
        </fieldset>
      </fieldset>
ENDHTML;

    return $strReturn;
  }

  /**
   *
   * @param string $p_strLikeCondition
   * @return array <EntityType>
   */
  public function getUsageTypes($p_strLikeCondition=""){
    return EntityTypePeer::findUsageType($p_strLikeCondition);
  }

  /**
   *
   * @param string $p_strInnerDiv
   * @param string $p_strCurrentPath
   * @param array <DataFile> $p_oCurrentDataFileArray
   * @param bool $p_bEdit
   * @return string
   */
  public function getFileBrowser($p_strMainDiv, $p_strInnerDiv, $p_strCurrentPath, $p_strTopPath, $p_oCurrentDataFileArray, $p_iRequestType, $p_bEdit, $p_iProjectId=0, $p_iExperimentId=0, $p_strReturnUrl=""){
    $strReturn = "";

    $oAuthorizer = Authorizer::getInstance();

    $oExperiment = null;
    if($p_iExperimentId){
      $oExperiment = ExperimentPeer::find($p_iExperimentId);
    }

    //create a friendly looking path
    $strCurrentFriendlyPath = get_friendlyPath($p_strCurrentPath);
    
    //provide breadcrumbs for file browser
    $strLocationPath = "";
    $strLocationArray = explode("/", $strCurrentFriendlyPath);
    array_shift($strLocationArray);
    $strLocationLinks = "";
    foreach($strLocationArray as $iLocationIndex=>$strLocation){
      $strLocationLinks .= "/".$strLocation;
      if($iLocationIndex > 0){
      $strLocationPath = $strLocationPath .<<< ENDHTML
       / <a href="/warehouse/projecteditor/project/$p_iProjectId/experiment/$p_iExperimentId/data?path=$strLocationLinks&uploadType=$p_iRequestType&parent=$p_strMainDiv&div=$p_strInnerDiv&toppath=$p_strTopPath">
            $strLocation
         </a>
ENDHTML;
      }else{
        $strLocationPath = $strLocationPath .<<< ENDHTML
       / $strLocation
ENDHTML;
    }
    }

    $strLastDirectoryName = end($strLocationArray);
    $iCheckAllEntityId = self::getEntityTypeIdByParent($strLastDirectoryName);

    $strDeleteHeader = ($p_bEdit) ? "Delete" : "";

    $strReturn .= <<< ENDHTML
      <div style="border: 0px solid rgb(102, 102, 102); overflow: auto; width: 100%; padding: 0px; margin: 0px;">
        <fieldset style="width: 100%;">
          <fieldset>
            <legend>Upload New File</legend>
            <div id="$p_strInnerDiv">
              <table cellpadding="1" cellspacing="1" style="margin-top:15px;">
                <thead>
                  <th valign="top" colspan="6" style="white-space:normal">
                    Location:&nbsp;&nbsp;
                    <span style="font-weight:normal;">$strLocationPath</span>
                  </th>
                </thead>
                
                <tr>
                  <td width="1">
                    <input id="checkAll" type="checkbox" name="checkAll" onClick="setAllCheckBoxes('frmProject', 'dataFile[]', this.checked, $p_iExperimentId);setFilesToDelete('frmProject', 'dataFile[]', 'cbxDelete', $p_iExperimentId, '$p_strInnerDiv-deleteLink', $iCheckAllEntityId);"/>
                    <input type="hidden" id="cbxDelete" name="deleteFiles" value=""/>
                  </td>
                  <td width="1"></td>
                  <td width="80%"><b>Name</b></td>
                  <td><b>Directories</b></td>
                  <td><b>Files</b></td>
                  <td><b>Manage</b></td>
                </tr>
ENDHTML;

          if(!empty($p_oCurrentDataFileArray)){
            /* @var $oDataFile DataFile */
            foreach($p_oCurrentDataFileArray as $iIndex => $oDataFile){
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

              $iEntityId = self::getEntityId($strFileName, $iDataFileId);
              $iEntityTypeId = self::getEntityTypeId($strFileName, $iDataFileId);

              /*
              if(preg_match("/Experiment-([0-9])+/", $strFileName)){
                $iEntityId = $oDataFileLink->getExperimentId();
                $iEntityTypeId = 3;
              }elseif(preg_match("/Trial-([0-9])+/", $strFileName)){
                $iEntityId = $oDataFileLink->getTrialId();
                $iEntityTypeId = 4;
              }elseif(preg_match("/Rep-([0-9])+/", $strFileName)){
                $iEntityId = $oDataFileLink->getRepId();
                $iEntityTypeId = 5;
              }else{
                $iEntityId = $iDataFileId;
                $iEntityTypeId = 112;
              }
              */

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
              $strFileFriendlyPath = $oDataFile->getFriendlyPath();

              //default the link to a file.  if an image, put in lightbox (png,jpg,gif)
              $strThisLink = <<< ENDHTML
                <a title="$strTooltip"
                   href="/data/get$strFileFriendlyPath" $strLightbox>
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
              if( $oDataFile->getDirectory()==1 ){
                $strDirectory = <<< ENDHTML
                    <a style="border:0px;" title="$strTooltip"
                       href="/warehouse/projecteditor/project/$p_iProjectId/experiment/$p_iExperimentId/data?path=$strFileFriendlyPath&uploadType=$p_iRequestType&parent=$p_strMainDiv&div=$p_strInnerDiv&toppath=$p_strTopPath">
                      <img src='/components/com_warehouse/images/icons/folder.gif' border='0'/>
                    </a>
                    $strCurationStatus
ENDHTML;

                //$strDirectory = "<img src='/components/com_warehouse/images/icons/folder.gif' border='0'/>";

                $strThisLink = <<< ENDHTML
                <a title="$strTooltip"
                   href="/warehouse/projecteditor/project/$p_iProjectId/experiment/$p_iExperimentId/data?path=$strFileFriendlyPath&uploadType=$p_iRequestType&parent=$p_strMainDiv&div=$p_strInnerDiv&toppath=$p_strTopPath">
                  $strFileName
                </a>
                $strCurationStatus
ENDHTML;
              }
              
              //exclude generated pics from the listing
              if($strFileName != Files::GENERATED_PICS){
                if(preg_match("/Trial-([0-9])+/", $strFileName) ||
                   preg_match("/Rep-([0-9])+/", $strFileName) ||
                   //preg_match("/Analysis/", $strFileName) ||
                   //preg_match("/Documentation/", $strFileName) ||
                  StringHelper::contains($strCurrentFriendlyPath, "Rep-([0-9])+")){
                  $iDirectoryStatsArray = self::getDirectorySummary($strCurrentFriendlyPath, $strFileName);
                  $strDirectoryCount = $iDirectoryStatsArray[0];
                  $strFileCount = $iDirectoryStatsArray[1];

                  $strBgColor = ($iIndex%2==0) ? "even" : "odd";

                  $strDisabled = ($strFileCount === 0) ? StringHelper::EMPTY_STRING : "disabled";
                  $strDisabled = self::getDisabledCheckBox($strFileCount, $strFileName, $strCurrentFriendlyPath);

                  $strEditLink = "";
                  if(!preg_match("/Experiment-([0-9])+/", $strFileName) &&
                     !preg_match("/Trial-([0-9])+/", $strFileName) &&
                     !preg_match("/Rep-([0-9])+/", $strFileName)){
                    $strEditLink = <<< ENDHTML
                      [<a class="modal" href="/warehouse/projecteditor/editdatafile?path=$strCurrentFriendlyPath&format=ajax&dataFileId=$iDataFileId&projectId=$p_iProjectId&experimentId=$p_iExperimentId&return=$p_strReturnUrl">Edit</a>]&nbsp;&nbsp;
ENDHTML;
                  }

                  if($oExperiment){
                    if($oAuthorizer->canDelete($oExperiment)){
                      if(!StringHelper::hasText($strDisabled)){
                        $strEditLink .= <<< ENDHTML
                        [<a class="modal" href="/warehouse/projecteditor/delete?path=$strCurrentFriendlyPath&format=ajax&eid=$iEntityId&etid=$iEntityTypeId&return=$p_strReturnUrl" title="Remove $strFileName">Delete</a>]
ENDHTML;
                      }else{
                        $strEditLink .= <<< ENDHTML
                        [<a href="javascript:void(0);" title="Unable to delete until files are removed." class="grayLinks">Delete</a>]
ENDHTML;
                      }
                    }
                  }
                  
                  $strReturn .= <<< ENDHTML
                    <tr class="$strBgColor">
                      <td><input id="$p_iExperimentId" $strDisabled type="checkbox" name="dataFile[]" value="$iEntityId" onClick="setFilesToDelete('frmProject', 'dataFile[]', 'cbxDelete', $p_iExperimentId, '$p_strInnerDiv-deleteLink', $iEntityTypeId);"/></td>
                      <td>$strDirectory</td>
                      <td>$strThisLink</td>
                      <td>$strDirectoryCount</td>
                      <td>$strFileCount</td>
                      <td nowrap="">$strEditLink</td>
                    </tr>
ENDHTML;
                }//name or path has Trial-
              }//end .Generated_Pics
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

    $strUploadButton = self::getUploadButton($strCurrentFriendlyPath, $p_strInnerDiv, $p_iRequestType, $p_strCurrentPath, $p_iProjectId, $p_iExperimentId, $p_strReturnUrl);
    $strCreateDirButton = self::getCreateDirectoryButton($strCurrentFriendlyPath, $p_strInnerDiv, $p_strCurrentPath, $p_iProjectId, $p_iExperimentId);
    $strFilmstripButton = self::getFilmstripButton($strCurrentFriendlyPath, $p_strInnerDiv);
    $strDeleteButton = self::getDeleteButton($oAuthorizer, $oExperiment, $p_strInnerDiv);

    // Don't show buttons for the Generated_Pics directory
    $strPattern = "/".Files::GENERATED_PICS."/";

      if(!preg_match($strPattern, $p_strCurrentPath)){
        $strReturn .= <<< ENDHTML
              <tr>
                <td colspan="6">
                  <div class="sectheaderbtn">
                    $strUploadButton $strCreateDirButton $strFilmstripButton $strDeleteButton
ENDHTML;

        $strReturn .= <<< ENDHTML
                  <!--
                  <div id="$p_strInnerDiv-curate" class="editorInputFloat editorInputMargin">
                    <a title="Request a directory or file to be curated." href="javascript:void(0);" onClick="document.getElementById('frmProject').action='/warehouse/projecteditor/savedatafilecuraterequest';document.getElementById('frmProject').submit();" style="border:0px">
                      <img src="/components/com_projecteditor/images/buttons/CurateRequest.png" border="0" alt="Request a directory or file to be curated."/>
                    </a>
                  </div>
                  -->
                  </div>
                </td>
              </tr>
ENDHTML;
      }

    $strReturn .= <<< ENDHTML
                </table>
                    <!--
                    <div id="legend" class="topSpace10">
                      <span style="font-weight:bold;">Legend:</span>  &nbsp;&nbsp;
                      <span class="curateSubmitted">*</span> Curation Requested &nbsp;&nbsp;
                      <span class="curateComplete">*</span> Curated &nbsp;&nbsp;
                      <span class="curateIncomplete">*</span> Incomplete
                    </div>
                    -->
                   </div>
                  </fieldset>
                </fieldset>
              </div>
ENDHTML;

    return $strReturn;

  }

  private function getCurrentLocation($p_strCurrentPath){
    $strPathArray = explode("/", $p_strCurrentPath);
    array_shift($strPathArray);
    array_shift($strPathArray);
    array_shift($strPathArray);
    return $strPathArray;
  }

  private function getDirectorySummary($p_strCurrentPath, $p_strDirectoryName){
    $strLookupPath = $p_strCurrentPath."/".$p_strDirectoryName;
    return DataFilePeer::getDirectorySummary($strLookupPath);
  }

  private function getDisabledCheckBox($p_iFileCount, $p_strFileName, $p_strCurrentFriendlyPath){
    $strReturn = "";
//    if(preg_match("/Trial-([0-9])+/", $p_strFileName) ||
//       preg_match("/Rep-([0-9])+/", $p_strFileName)){
//        if($p_iFileCount > 0){
//          $strReturn = "disabled";
//        }
//    }
    if($p_iFileCount > 0){
      $strReturn = "disabled";
}
    return $strReturn;
  }

  /**
   * Gets the upload button
   * @param string $p_strCurrentFriendlyPath
   * @param string $p_strInnerDiv
   * @param int $p_iRequestType
   * @param string $p_strCurrentPath
   * @param int $p_iProjectId
   * @param int $p_iExperimentId
   * @param int $p_strReturnUrl
   * @return string
   */
  private function getUploadButton($p_strCurrentFriendlyPath, $p_strInnerDiv, $p_iRequestType, $p_strCurrentPath, $p_iProjectId, $p_iExperimentId, $p_strReturnUrl){
    $strReturn = "";
    if(StringHelper::contains($p_strCurrentFriendlyPath, "Rep-([0-9])+") ||
       StringHelper::contains($p_strCurrentFriendlyPath, "Documentation") ||
       StringHelper::contains($p_strCurrentFriendlyPath, "Analysis")){
      $strReturn = <<< ENDHTML
        <a id="$p_strInnerDiv-upload" title="Upload a new file."
           tabindex="" class="button2 modal"
           href="/warehouse/projecteditor/uploadform?format=ajax&div=$p_strInnerDiv&uploadType=$p_iRequestType&path=$p_strCurrentPath&projid=$p_iProjectId&experimentId=$p_iExperimentId&return=$p_strReturnUrl">
          Upload File
        </a>
ENDHTML;
    }
    return $strReturn;
  }

  /**
   * Gets the create directory button
   * @param string $p_strCurrentFriendlyPath
   * @param string $p_strInnerDiv
   * @param string $p_strCurrentPath
   * @param int $p_iProjectId
   * @param int $p_iExperimentId
   * @return string
   */
  private function getCreateDirectoryButton($p_strCurrentFriendlyPath, $p_strInnerDiv, $p_strCurrentPath, $p_iProjectId, $p_iExperimentId){
    $strReturn = "";
    if(StringHelper::contains($p_strCurrentFriendlyPath, "Rep-([0-9])+") ||
       StringHelper::contains($p_strCurrentFriendlyPath, "Documentation") ||
       StringHelper::contains($p_strCurrentFriendlyPath, "Analysis")){
      $strReturn = <<< ENDHTML
        <a id="$p_strInnerDiv-mkdir" title="Create a new directory."
           tabindex="" class="button2 modal"
           href="/warehouse/projecteditor/mkdir?format=ajax&path=$p_strCurrentPath&projid=$p_iProjectId&experimentId=$p_iExperimentId">
          Create Directory
        </a>
ENDHTML;
    }
    return $strReturn;
  }

  /**
   * Gets the filmstrip button
   * @param string $p_strCurrentFriendlyPath
   * @param string $p_strInnerDiv
   * @return string
   */
  private function getFilmstripButton($p_strCurrentFriendlyPath, $p_strInnerDiv){
    $strReturn = "";
    if(StringHelper::contains($p_strCurrentFriendlyPath, "Rep-([0-9])+") ||
       StringHelper::contains($p_strCurrentFriendlyPath, "Documentation") ||
       StringHelper::contains($p_strCurrentFriendlyPath, "Analysis")){
      $strReturn = <<< ENDHTML
        <a id="$p_strInnerDiv-film" title="Select png, jpg, or gif images for the experiment filmstrip."
           tabindex="" class="button2" href="javascript:void(0);"
           onClick="document.getElementById('frmProject').action='/warehouse/projecteditor/savefilmstrip';document.getElementById('frmProject').submit();">
          Filmstrip Photo
        </a>
ENDHTML;
    }
    return $strReturn;
  }

  /**
   * Gets the delete button
   * @param Authorizer $p_oAuthorizer
   * @param Experiment $p_oExperiment
   * @param string $p_strInnerDiv
   * @return string
   */
  private function getDeleteButton($p_oAuthorizer, $p_oExperiment, $p_strInnerDiv){
    $strReturn = "";
    if($p_oExperiment){
      if($p_oAuthorizer->canDelete($p_oExperiment)){
        $strReturn .= <<< ENDHTML
              <a id="$p_strInnerDiv-deleteLink" title="Delete the selected file(s)"
                 tabindex="" href="/warehouse/projecteditor/delete?format=ajax" class="button2 modal">Delete</a>
ENDHTML;
      }
    }
    return $strReturn;
  }

  /**
   * Gets the entity_id of an object by the directory name
   * @param string $p_strDataFileName
   * @param int $p_iDataFileId
   * @return int
   */
  private function getEntityId($p_strDataFileName, $p_iDataFileId){
    /*@var $oDataFileLink DataFileLink */
    $oDataFileLink = DataFileLinkPeer::retrieveByPK($p_iDataFileId);

    if(preg_match("/Trial-([0-9])+/", $p_strDataFileName)){
      return $oDataFileLink->getTrialId();
    }elseif(preg_match("/Rep-([0-9])+/", $p_strDataFileName)){
      return $oDataFileLink->getRepId();
    }
    return $p_iDataFileId;
  }

  /**
   * Gets the entity_type_id of an object by the directory name
   * @param string $p_strDataFileName
   * @param int $p_iDataFileId
   * @return int
   */
  private function getEntityTypeId($p_strDataFileName, $p_iDataFileId){
    /*@var $oDataFileLink DataFileLink */
    $oDataFileLink = DataFileLinkPeer::retrieveByPK($p_iDataFileId);

    if(preg_match("/Trial-([0-9])+/", $p_strDataFileName)){
      return DomainEntityType::ENTITY_TYPE_TRIAL;
    }elseif(preg_match("/Rep-([0-9])+/", $p_strDataFileName)){
      return DomainEntityType::ENTITY_TYPE_REPETITION;
    }
    return DomainEntityType::ENTITY_TYPE_DATA_FILE;
  }

  /**
   * Gets the entity_type_id of an object by the parent directory name
   * @param string $p_strParentDataFileName
   * @return int
   */
  private function getEntityTypeIdByParent($p_strParentDataFileName){
    if(preg_match("/Experiment-([0-9])+/", $p_strParentDataFileName)){
      return DomainEntityType::ENTITY_TYPE_TRIAL;
    }elseif(preg_match("/Trial-([0-9])+/", $p_strParentDataFileName)){
      return DomainEntityType::ENTITY_TYPE_REPETITION;
    }
    return DomainEntityType::ENTITY_TYPE_DATA_FILE;
  }

}
