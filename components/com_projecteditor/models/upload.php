<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.component.view' );

require_once('base.php');
require_once 'lib/data/AuthorizationPeer.php';
require_once 'lib/data/Person.php';
require_once 'lib/data/Project.php';

class ProjectEditorModelUpload extends ProjectEditorModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

  public function getUploadedFilesHTML($p_strPath, $p_iRequestType, $p_strDiv, $p_strUsageTypes){
    $strReturn = <<< ENDHTML
        <input type="hidden" name="div" value="$p_strDiv" id="div"/>
        <input type="hidden" name="uploadType" value="$p_iRequestType" id="uploadType"/>
        <input type="hidden" name="path" value="$p_strPath" id="path"/>
        <table style="border:0px">
          <tr>
            <td width="1" nowrap="" class="editorLabel">
              Drawing Type:
            </td>
            <td>$p_strUsageTypes</td>
          </tr>
          <tr>
            <td width="1" nowrap="">
              <label for="$p_strDiv-title" class="editorLabel">Title:</label>
            </td>
            <td><input id="$p_strDiv-title" type="text" name="title" class="editorInputSize"/></td>
          </tr>
          <tr>
            <td width="1" nowrap="">
              <label for="$p_strDiv-desc" class="editorLabel">Description:</label>
            </td>
            <td><textarea id="$p_strDiv-desc" name="desc" class="editorInputSize"></textarea></td>
          </tr>
          <tr>
            <td width="1" nowrap="">
              <label for="$p_strDiv-file" class="editorLabel">File:</label>
            </td>
            <td><input id="$p_strDiv-file" type="file" name="upload" class="editorInputSize"/></td>
          </tr>
          <tr>
            <td colspan="2">
              <!--<input type="button" value="Upload" onClick="uploadDataFile('/warehouse/projecteditor/upload', 'drawingInput')"/>-->
              <input type="button" value="Upload" onClick="getMootoolsForm('frmProject', 'drawingInput', '/warehouse/projecteditor/upload')";
            </td>
          </tr>
        </table>
ENDHTML;

    return $strReturn;
  }
  
}

?>