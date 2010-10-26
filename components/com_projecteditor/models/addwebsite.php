<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('project.php');

class ProjectEditorModelAddWebsite extends ProjectEditorModelProject{
	
  public function getProjectLinksHtml($p_strPrefix, $p_oEntityArray){
    $strReturn = StringHelper::EMPTY_STRING;

    /* @var $oEntity ProjectHomepage */
    foreach ($p_oEntityArray as $iIndex=>$oEntity){
      $strCaption = $oEntity->getCaption();
      $strUrl = $oEntity->getUrl();
      $strInputDiv = $p_strPrefix."-".$iIndex."Input";
      $strCaptionFieldArray = "website[]";
      $strCaptionFieldPicked = $p_strPrefix."CaptionPicked";
      $strUrlFieldArray = "url[]";
      $strUrlFieldPicked = $p_strPrefix."UrlPicked";
      $strRemoveDiv = $p_strPrefix."-".$iIndex."Remove";

      $strReturn .= <<< ENDHTML

          <div id="$strInputDiv" class="editorInputFloat editorInputSize">
            <input type="hidden" name="$strCaptionFieldArray" value="$strCaption"/>
            <input type="hidden" name="$strUrlFieldArray" value="$strUrl"/>
            $strCaption (<a href='$strUrl'>view</a>)
          </div>
          <div id="$strRemoveDiv" class="editorInputFloat editorInputButton">
            <a href="javascript:void(0);" title="Remove $strCaption." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/removewebsite?format=ajax', '$p_strPrefix', $iIndex, 'websitePicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
          </div>
          <div class="clear"></div>

ENDHTML;

    }

    return $strReturn;
  }
  
}

?>