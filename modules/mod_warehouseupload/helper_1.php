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
  public function getFileBrowser($p_strMainDiv, $p_strInnerDiv, $p_strCurrentPath, $p_strTopPath, $p_oCurrentDataFileArray, $p_iRequestType, $p_bEdit, $p_iProjectId=0, $p_iExperimentId=0){
    $strReturn = "";

    //create a friendly looking path
    $strCurrentFriendlyPath = get_friendlyPath($p_strCurrentPath);
    echo $strCurrentFriendlyPath."<br>";

    //provide breadcrumbs for file browser
    $strLocationPath = "/nees/home";
    $strLocationArray = self::getCurrentLocation($p_strCurrentPath);
    $strLocationLinks = "/nees/home";
    foreach($strLocationArray as $strLocation){
      $strLocationLinks .= "/".$strLocation;
//      $strLocationPath = $strLocationPath .<<< ENDHTML
//       / <a href="javascript:void(0);"
//           onClick="getMootools('/warehouse/projecteditor/filebrowser?path=$strLocationLinks&format=ajax&projid=$p_iProjectId&experimentId=$p_iExperimentId&uploadType=$p_iRequestType&parent=$p_strMainDiv&div=$p_strInnerDiv&toppath=$p_strTopPath','$p_strMainDiv');">$strLocation</a>
//ENDHTML;
      $strLocationPath = $strLocationPath .<<< ENDHTML
       / <a href="/warehouse/projecteditor/project/$p_iProjectId/experiment/$p_iExperimentId/data?path=$strLocationLinks&uploadType=$p_iRequestType&parent=$p_strMainDiv&div=$p_strInnerDiv&toppath=$p_strTopPath">
            $strLocation
         </a>
ENDHTML;
    }

    $strDeleteHeader = ($p_bEdit) ? "Delete" : "";

    /*
    $strGoBackLink = "";
    if($p_strCurrentPath != $p_strTopPath){
      $strCurrentPathArray = explode("/", $p_strCurrentPath);
      array_pop($strCurrentPathArray);

      $strGoBackPath = implode("/", $strCurrentPathArray);
      
      $strGoBackLink = <<< ENDHTML
        <a href="javascript:void(0);"
           onClick="getMootools('/warehouse/projecteditor/filebrowser?path=$strGoBackPath&format=ajax&projid=$p_iProjectId&experimentId=$p_iExperimentId&uploadType=$p_iRequestType&parent=$p_strMainDiv&div=$p_strInnerDiv&toppath=$p_strTopPath','$p_strMainDiv');">... go back</a>
ENDHTML;
    }
    */

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
                  <td width="1"><input id="checkAll" type="checkbox" name="checkAll" onClick="setAllCheckBoxes('frmProject', 'dataFile[]', this.checked, $p_iExperimentId);"/></td>
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

                $iDirectoryStatsArray = self::getDirectorySummary($strCurrentFriendlyPath, $strFileName);
                $strDirectoryCount = $iDirectoryStatsArray[0];
                $strFileCount = $iDirectoryStatsArray[1];

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
                $strBgColor = ($iIndex%2==0) ? "even" : "odd";

                $strReturn .= <<< ENDHTML
                  <tr class="$strBgColor">
                    <td><input id="$p_iExperimentId" type="checkbox" name="dataFile[]" value="$iDataFileId"/></td>
                    <td>$strDirectory</td>
                    <td>$strThisLink</td>
                    <td>$strDirectoryCount</td>
                    <td>$strFileCount</td>
                    <td>
                      <!--<a href="javascript:void(0);" title="Remove $strFileName" style="border-bottom: 0px" onClick=""><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>-->
                      [<a class="modal" href="/warehouse/projecteditor/editdatafile?path=$strCurrentFriendlyPath&format=ajax&dataFileId=$iDataFileId">Edit</a>]&nbsp;&nbsp;[Delete]
                    </td>
                  </tr>
ENDHTML;
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

    // Don't show buttons for the Generated_Pics directory
    $strPattern = "/".Files::GENERATED_PICS."/";
    if(!preg_match($strPattern, $p_strCurrentPath)){
      $strReturn .= <<< ENDHTML
              <tr>
                <td colspan="6">
                  <div id="$p_strInnerDiv-upload" class="editorInputFloat editorInputMargin">
                    <a class="modal" href="/warehouse/projecteditor/uploadform?format=ajax&div=$p_strInnerDiv&uploadType=$p_iRequestType&path=$p_strCurrentPath&projid=$p_iProjectId&experimentId=$p_iExperimentId" style="border:0px">
                      <img src="/components/com_projecteditor/images/buttons/UploadFile.png" border="0"/>
                    </a>
                  </div>
                  <div id="$p_strInnerDiv-mkdir" class="editorInputFloat editorInputMargin">
                    <a class="modal" href="/warehouse/projecteditor/mkdir?format=ajax&path=$p_strCurrentPath&projid=$p_iProjectId&experimentId=$p_iExperimentId" style="border:0px">
                      <img src="/components/com_projecteditor/images/buttons/CreateDirectory.png" border="0"/>
                    </a>
                  </div>
                  <div id="$p_strInnerDiv-film" class="editorInputFloat editorInputMargin">
                    <a href="javascript:void(0);" onClick="document.getElementById('frmProject').action='/warehouse/projecteditor/savefilmstrip';document.getElementById('frmProject').submit();" style="border:0px">
                      <img src="/components/com_projecteditor/images/buttons/FilmstripPhoto.png" border="0"/>
                    </a>
                  </div>
                  <div id="$p_strInnerDiv-curate" class="editorInputFloat editorInputMargin">
                    <a href="javascript:void(0);" onClick="document.getElementById('frmProject').action='/warehouse/projecteditor/savedatafilecuraterequest';document.getElementById('frmProject').submit();" style="border:0px">
                      <img src="/components/com_projecteditor/images/buttons/CurateRequest.png" border="0"/>
                    </a>
                  </div>
                  <div class="clear"></div>
                </td>
              </tr>
ENDHTML;
    }

    $strReturn .= <<< ENDHTML
                </table>
                    <div id="legend" class="topSpace10">
                      <span style="font-weight:bold;">Legend:</span>  &nbsp;&nbsp;
                      <span class="curateSubmitted">*</span> Curation Requested &nbsp;&nbsp;
                      <span class="curateComplete">*</span> Curated &nbsp;&nbsp;
                      <span class="curateIncomplete">*</span> Incomplete
                    </div>
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

}
