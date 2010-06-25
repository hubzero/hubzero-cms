<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once('api/org/nees/html/LinkParamsHtml.php');

class CurateViewForm extends JView{
	
  function display($tpl = null){
  	$oLinkParamsHtml = new LinkParamsHtml();
  	$oLinkParamsHtml->append("format", "ajax");
  	$oLinkParamsHtml->store("format", "ajax");
  	
  	#form input type (input, textarea, etc)
  	$nInputType = JRequest::getVar('type');
  	$oLinkParamsHtml->append("type", $nInputType);
  	$oLinkParamsHtml->store("type", $nInputType);
  	
  	#if a page.size isn't passed, set the default index to 25 rows
    $sInputName = JRequest::getVar('name');
    
    $this->assignRef( 'inputFieldName', $sInputName );
    $oLinkParamsHtml->append("name", $sInputName);
    $oLinkParamsHtml->store("name", $sInputName);
    
    #if a page.size isn't passed, set the default index to 25 rows
    $sInputValue = JRequest::getVar('value');
    $this->assignRef( 'inputFieldValue', $sInputValue );
    $oLinkParamsHtml->append("value", $sInputValue);
    $oLinkParamsHtml->store("value", $sInputValue);
    
    #grab the label for the input field
    $sLabel = JRequest::getVar('label');
    $this->assignRef( 'inputFieldLabel', $sLabel );
    $oLinkParamsHtml->append("label", $sLabel);
    $oLinkParamsHtml->store("label", $sLabel);
    
    #is the curent object curated or not?
    $nCurated = JRequest::getVar('curated');
    
    $oLinkParamsHtml->append("curated", $nCurated);
    $oLinkParamsHtml->store("curated", $nCurated);
    
    #grab the return div id
    $sReturnDivId = JRequest::getVar('return');
    $oLinkParamsHtml->append("return", $sReturnDivId);
    $oLinkParamsHtml->store("return", $sReturnDivId);
    
    #grab the method (task)
    $sMethod = JRequest::getVar('method');
    $oLinkParamsHtml->append("method", $sMethod);
    $oLinkParamsHtml->store("method", $sMethod);
    
    $_SESSION['METHOD'] = $sMethod;
    
    #grab the column to update
    $sColumn = JRequest::getVar('column');
    $oLinkParamsHtml->append("column", $sColumn);
    $oLinkParamsHtml->store("column", $sColumn);
    
    #set the task
    $oLinkParamsHtml->append("task", "update");
    $oLinkParamsHtml->store("task", "update");
      
    #call the action to save the input.  return to the original div.
    //$sFormAction = "/?option=com_curate&task=saveform&curated=".$nCurated."&type=".$nInputType."&name=".$sInputName."&value=".$sInputValue."&label=".$sLabel."&format=ajax&return=".$sReturnDivId;
    $sFormAction = "/?option=com_curate". "&". $oLinkParamsHtml->toHtml();
    
    /*
    if($nCurated > 0){
      $oLinkParamsHtml->append("task", "update");
      $oLinkParamsHtml->store("task", "update");
      //$sFormAction = "/?option=com_curate&task=update".$sMethod."&curated=".$nCurated."&type=".$nInputType."&name=".$sInputName."&value=".$sInputValue."&label=".$sLabel."&format=ajax&return=".$sReturnDivId;
      //$sFormAction = "/?option=com_curate&task=update&curated=".$nCurated."&type=".$nInputType."&name=".$sInputName."&value=".$sInputValue."&label=".$sLabel."&format=ajax&return=".$sReturnDivId;
      $sFormAction = $sFormAction . "&".$oLinkParamsHtml->toHtml();
    }*/
    
    $_SESSION['LINK_PARAMS'] = serialize($oLinkParamsHtml);
    
    #decide what input field to show to the user.
    switch ($nInputType) {
    case 1:
      echo $this->getInputField($sInputName, $sInputValue, $sLabel, $nCurated, $sFormAction, $sReturnDivId);
      break;
    case 2:
      echo $this->getTextArea($sInputName, $sInputValue, $sLabel, $nCurated, $sFormAction, $sReturnDivId);
      break;
    }
  }
  
  function getInputField($p_sName, $p_sValue, $p_sLabel, $p_nCurated, $p_sFormAction, $p_sReturnDivId){
  	/*
  	 * Initialize return to a simple input field.
  	 * If the project is curated, allow for updating 
  	 * the current record in the database through 
  	 * an ajax request.
  	 */
  	$sReturn = $p_sLabel.": <input type='text' id='$p_sName' name='$p_sName' value='$p_sValue'/>";
  	if($p_nCurated > 0){
  	  $sReturn = <<< ENDHTML
  	    $p_sLabel: <input type="text" id="$p_sName" name="$p_sName" value="$p_sValue"/>
  	    <a href="javascript:void(0);" 
  	       onClick="saveInput('$p_sFormAction', '$p_sName', '$p_sReturnDivId');">
  	      Save
  	    </a>
ENDHTML;
  	}
    return $sReturn;  	
  }
  
  function getTextArea($p_sName, $p_sValue, $p_sLabel, $p_nCurated, $p_sFormAction, $p_sReturnDivId){
  	/*
  	 * Initialize return to a simple input field.
  	 * If the project is curated, allow for updating 
  	 * the current record in the database through 
  	 * an ajax request.
  	 */
  	$sReturn = $p_sLabel.":<br>".
  				"<textarea id='$p_sName' name='$p_sName' cols='80' rows='10'>$p_sValue</textarea>";
  	if($p_nCurated > 0){
  	  $sReturn = <<< ENDHTML
  	    $p_sLabel: 
  	    <a href="javascript:void(0);" 
  	       onClick="saveInput('$p_sFormAction', '$p_sName', '$p_sReturnDivId');">
  	       Save
  	    </a><br> 
  	    <textarea id="$p_sName" name="$p_sName" cols="80" rows="10">$p_sValue</textarea>
ENDHTML;
  	}
    return $sReturn;  	
  }
  
}//end class
?>
