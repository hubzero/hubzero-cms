<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.component.view' );

require_once('base.php');
require_once 'lib/data/AuthorizationPeer.php';
require_once 'lib/data/Person.php';
require_once 'lib/data/Project.php';
require_once 'api/org/nees/static/ProjectEditor.php';

class ProjectEditorModelUploadForm extends ProjectEditorModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

  public function getUploadFormHTML($p_strPath, $p_iRequestType, $p_strDiv, $p_strUsageTypes, $p_iProjectId=0, $p_iExperimentId=0){
    $strHelp = "";
    $strTag = "";
    $strNotes = "";
    switch($p_iRequestType){
      case Files::DRAWING:
          $strTag = "Drawing Type:";
          $strNotes = ProjectEditor::PHOTO_NOTE;
          break;
      case Files::DATA;
          $strTag = "Default Tool:";
          $strNotes = ProjectEditor::PHOTO_NOTE;
          break;
      case Files::IMAGE:
          $strTag = "Photo Type:";
          $strNotes = ProjectEditor::PHOTO_NOTE;
          break;
      case Files::VIDEO:
          $strTag = "Video Type:";
          $strNotes = ProjectEditor::MOVIE_FRAMES_NOTE;
          break;
      default :
          break;
    }

    $strFriendlyPath = get_friendlyPath($p_strPath);

    $strReturn = <<< ENDHTML
      <div><h2>Upload File</h2></div>
      <form  id="frmPopup" action="/warehouse/projecteditor/upload" method="post" enctype="multipart/form-data">
        <input type="hidden" name="projId" value="$p_iProjectId" id="project"/>
        <input type="hidden" name="experimentId" value="$p_iExperimentId" id="experiment"/>
        <input type="hidden" name="div" value="$p_strDiv" id="div"/>
        <input type="hidden" name="requestType" value="$p_iRequestType" id="uploadType"/>
        <input type="hidden" name="path" value="$p_strPath" id="path"/>
        <div class="information"><b>Destination:</b> $strFriendlyPath</div>
        <table style="border:0px;margin-top:10px;">
          <tr>
            <td width="1" nowrap="" class="editorLabel">
              $strTag
            </td>
            <td>$p_strUsageTypes</td>
          </tr>
          <tr>
            <td width="1" nowrap="">
              <label for="title" class="editorLabel">Title:<span class="requiredfieldmarker">*</span></label>
            </td>
            <td><input id="title" type="text" name="title" class="editorInputSize"/></td>
          </tr>
          <tr>
            <td width="1" nowrap="">
              <label for="desc" class="editorLabel">Description:</label>
            </td>
            <td><textarea id="desc" name="desc" class="editorInputSize"></textarea></td>
          </tr>
          <tr>
            <td width="1" nowrap="">
              <label for="file" class="editorLabel">File:<span class="requiredfieldmarker">*</span></label>
            </td>
            <td>
              <div id="uploadInput">
                <input id="file" type="file" name="upload[]" class="editorInputSize"/>
              </div>
              <div style="margin-top:10px;">
                <div class="editorInputFloat">
                 <span><nobr>Upload </nobr></span>
                </div>
                <div class="editorInputFloat" style="margin-left:5px;">
                    <select width="25" name="files_num" id="files_num">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                        <option value="15">15</option>
                    </select>
	        </div>
                <div class="editorInputFloat" style="margin-left:5px;"><span> file(s) at once </span></div>
                <div class="editorInputFloat" style="margin-left:5px;">
                  <input type="button" value="Display" onClick="getMootools('/warehouse/projecteditor/multiplefiles?format=ajax&files_num='+document.getElementById('files_num').value, 'uploadInput');"/>
                </div>
                <div class="clear"></div>
                <div class="topSpace20" style="font-size:11px;">
                  $strNotes
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="submit" id="button" value="Upload" onClick="uploadDataFile('frmPopup', '/warehouse/projecteditor/upload');"/>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <hr/>
              <!--
              <span style="color:#666666;font-size:12px;font-weight:bold;">When uploading more than 15 files or documents larger than 650.0MB, use <a href="/resources/pen">PEN</a>.</span>
              -->
              <span style="color:#666666;font-size:12px;font-weight:bold;">The maximum file size is 650.0MB.</span>
            </td>
          </tr>
        </table>
      </form>
ENDHTML;

    return $strReturn;
  }

  public function findOpeningTools(){
    return DataFilePeer::findOpeningTools();
  }

  public function findOpeningToolsHTML($p_strToolArray, $p_strTool=""){
    $strReturn = "<select id=\"cboTools\" name=\"tool\"  class=\"editorInputSize\">
                           <option value=''>-No Tool-</option>";

    foreach($p_strToolArray as $strToolName){
      $strSelected = "";
      if($strToolName==$p_strTool){
            $strSelected = "selected";
      }
      $strReturn .= <<< ENDHTML
              <option $strSelected value="$strToolName">$strToolName</option>
ENDHTML;
    }
    $strReturn .= "</select>";
    return $strReturn;
  }

  /**
   *
   * @param EntityType $p_oEntityType
   * @param int $p_iNumFiles
   * @return boolean
   */
  public function validateVideoFrames($p_oEntityType, $p_iNumFiles){
    $bValid = true;
    if($p_oEntityType){
      $strUsageType = $p_oEntityType->getDatabaseTableName();
      if($strUsageType=="Video-Frames"){
        //validate the extension (only allow .zip and .tar)
        $counter = 0;
        while ($counter < $p_iNumFiles) {
          $fileName = $_FILES[ProjectEditor::UPLOAD_FIELD_NAME]['name'][$counter];
          $uploadedFileNameParts = explode('.',$fileName);
          $uploadedFileExtension = array_pop($uploadedFileNameParts);
          echo "$uploadedFileExtension<br>";
          if(strtolower($uploadedFileExtension) == "zip" || 
             strtolower($uploadedFileExtension) == "gz" ||
             strtolower($uploadedFileExtension) == "tar"){
            //do nothing
          }else{
            $bValid = false;
          }
          ++$counter;
        }
      }
    }
    return $bValid;
  }
  
}

?>