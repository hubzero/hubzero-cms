<?php 
  
  class UserRequest{
  	
  	/**
  	 * Whenever an input field has name="xxx[]", the user is providing 
  	 * a collection of values for the given input name.  Collect the 
  	 * information from the form and return a single array.
  	 *   
  	 * @param - $p_strFieldName
  	 * @return - array of input values.
  	 */
    public static function getMultipleValues($p_strFieldName){
  	  $strValueArray = array();
  	  
  	  $strInputArray = $_REQUEST[$p_strFieldName];
      while (list ($iKey,$strValue) = @each ($strInputArray)) {
	    array_push($strValueArray, $strValue);
	  }
	  return $strValueArray;
    }
    
    /**
  	 * Whenever an input field has name="xxx[]", the user is providing 
  	 * a collection of values for the given input name.  Collect the 
  	 * information from the form and return a single array.
  	 *   
  	 * @param - $p_strFieldName
  	 * @return - array of input values.
  	 */
    public static function getMultipleValuesHTML($p_strFieldName){
      $strReturn = "";
      if(isset($_SESSION[$p_strFieldName])){
        $strInputArray = $_SESSION[$p_strFieldName];
        foreach($strInputArray as $iIndex=>$strInput){
          $strInputDiv = $p_strFieldName."-".$iIndex."Input";
          $strFieldArray = $p_strFieldName."Array[]";
          $strFieldPicked = $p_strFieldName."Picked";
          $strRemoveDiv = $p_strFieldName."-".$iIndex."Remove";
          
          $strReturn .= <<< ENDHTML
	      
	      <div id="$strInputDiv" class="editorInputFloat editorInputSize">
	        <input type="hidden" name="$strFieldArray" value="$strInput"/>
	        $strInput
	      </div>
	      <div id="$strRemoveDiv" class="editorInputFloat editorInputButton">
	        <a href="javascript:void(0);" title="Remove $strInput." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/remove?format=ajax', '$p_strFieldName', $iIndex, '$strFieldPicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
	      </div>
	      <div class="clear"></div>

ENDHTML;
         
        }
      }
      return $strReturn;
    }
    
    public static function getTupleValuesHTML($p_strFieldName){
      $strReturn = "";
  	
      $strName = $p_strFieldName;
      if(isset($_SESSION[$strName])){
        $oTupleArray = unserialize($_SESSION[$strName]);
        foreach($oTupleArray as $iIndex=>$oTuple){
      	  $strField = $oTuple->getField1();
      	  $strFieldArray = $strField."Array[]";
      	  $strFieldPicked = $strField."Picked";
      	  $strFieldValue = $oTuple->getName().":".$oTuple->getValue();
      	  $strInputDivId = $strField."-".$iIndex."Input";
      	  $strRemoveDivId = $strField."-".$iIndex."Remove";
      	
	      $strReturn .= <<< ENDHTML
	      
	      <div id="$strInputDivId" class="editorInputFloat editorInputSize">
	        <input type="hidden" name="$strFieldArray" value="$strFieldValue"/>
	        $strFieldValue
	      </div>
	      <div id="$strRemoveDivId" class="editorInputFloat editorInputButton">
	        <a href="javascript:void(0);" title="Remove $strFieldValue" style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/remove?format=ajax', '$strField', $iIndex, '$strFieldPicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
	      </div>
	      <div class="clear"></div>

ENDHTML;
        }//end foreach
	  }
	  return $strReturn;
    }
    
  public static function getWebsiteTupleValuesHTML($p_strFieldName){
      $strReturn = "";
  	
      $strName = $p_strFieldName;
      if(isset($_SESSION[$strName])){
        $oTupleArray = unserialize($_SESSION[$strName]);
        foreach($oTupleArray as $iIndex=>$oTuple){
      	  $strField = $oTuple->getField1();
      	  $strFieldArray = $strField."Array[]";
      	  $strFieldPicked = $strField."Picked";
      	  $strFieldValue = $oTuple->getName()."^*".$oTuple->getValue();
      	  $strFieldValueDisplay = $oTuple->getName()." (<a href='".$oTuple->getValue()."'>view</a>)";
      	  $strInputDivId = $strField."-".$iIndex."Input";
      	  $strRemoveDivId = $strField."-".$iIndex."Remove";
      	
	      $strReturn .= <<< ENDHTML
	      
	      <div id="$strInputDivId" class="editorInputFloat editorInputSize">
	        <input type="hidden" name="$strFieldArray" value="$strFieldValue"/>
	        $strFieldValueDisplay
	      </div>
	      <div class="clear"></div>

ENDHTML;
        }//end foreach
	  }
	  return $strReturn;
    }
  	
  }

?>