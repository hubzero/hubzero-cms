<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once('api/org/nees/html/LinkParamsHtml.php');

class CurateViewFinished extends JView{
	
  function display($tpl = null){
  	#form input type (input, textarea, etc)
  	$oLinkParamsHtml = unserialize($_SESSION['LINK_PARAMS']);
  	$iInputType = $oLinkParamsHtml->get( 'type' );
  	
    $strInputName = $oLinkParamsHtml->get( 'name' );
    $this->assignRef( 'inputFieldName', $strInputName );
    
    #if a page.size isn't passed, set the default index to 25 rows
    $strInputValue = $oLinkParamsHtml->get( 'value' );
    $this->assignRef( 'inputFieldValue', $strInputValue );
    
    #grab the label for the input field
    $strLabel = $oLinkParamsHtml->get( 'label' );
    $this->assignRef( 'inputFieldLabel', $strLabel );
    
    #is the curent object curated or not?
    $iCuratedId = $oLinkParamsHtml->get( 'curated' );
    
    #grab the return div id
    $strReturnDivId = $oLinkParamsHtml->get( 'return' );
    
    /*
     * the method gets appended to the task call.
     * for example, if the method is name, the 
     * task is updatename
     */ 
    $strMethod = $oLinkParamsHtml->get( 'method' );
    
    $strtrColumn = $oLinkParamsHtml->get( 'column' );
    
    #set the task
    $oLinkParamsHtml->append("task", "showform");
    $oLinkParamsHtml->store("task", "showform");
    
    //save the params with the updated task
    $_SESSION['LINK_PARAMS'] = serialize($oLinkParamsHtml);
    
    /*
     * initialize the links for the ajax calls.  
     * we will either edit or save.
     */
	$oLinkParamsHtml->append("type", "1");
    $strInputFieldLink = "/curate?".$oLinkParamsHtml->toHtml();
    
    $oLinkParamsHtml->append("type", "2");
    $strTextAreaLink = "/curate?".$oLinkParamsHtml->toHtml();
 
    $strColumn = $oLinkParamsHtml->get( 'column' );
    
    $oModel =& $this->getModel();
    $strInputValue = $oModel->getCuratedObjectAttribute($strColumn, $iCuratedId);
    
    #decide what input field to show to the user.
    switch ($iInputType) {
    case 1:
      echo $oModel->getAjaxHandler($strLabel, $strInputValue, $strInputFieldLink, $iCuratedId, $strReturnDivId, $strInputName);
      break;
    case 2:
      echo $oModel->getAjaxTextAreaHandler($strLabel, $strInputValue, $strTextAreaLink, $iCuratedId, $strReturnDivId, $strInputName);
      break;
    }
    
  }
  
  
}//end class
?>
