<?php 

  require_once('api/org/nees/oracle/util/DbHelper.php');
  require_once('api/org/nees/oracle/util/DbParameter.php');
  require_once('api/org/nees/oracle/util/DbStatement.php');
  require_once('neesconfiguration.php');

  class CurateHtml{
  
    /**
     * Returns an HTML dropdown string of curation object types
     */
    public static function getSelectedCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $p_sFoundCurationObjectType, $p_iRowIndex){
  	  $sReturn = "<select id=cboObjectType".$p_iRowIndex." name=cboObjectType".$p_iRowIndex."  class=\"spreadsheetInput\" disabled>";
  	  $sReturn = $sReturn . "<option value=''>-Select-</option>";
  	  
  	  /*
  	   * if we have a found curation object type, compare it to
  	   * the values in the list.  if a match, mark the option 
  	   * object as selected.  
  	   */ 
  	  foreach($p_oCurationObjectTypeArray as $oCurationObjectType){
  	    $sCurationObjectType = $oCurationObjectType['OBJECT_TYPE'];
  	    
  	    $sSelected = "";
  	    if($sCurationObjectType===$p_sFoundCurationObjectType &&
  	       !empty($p_sFoundCurationObjectType)){
  	  	  $sSelected = "selected";
  	    }
  	    $sReturn = $sReturn . "<option ".$sSelected.">".$sCurationObjectType."</option>";
  	  }
  	  $sReturn = $sReturn."</select>";
  	  return $sReturn;
    }//end getCurationObjectTypesAsHtml
    
    /**
     * Returns an HTML dropdown string of curation object types
     */
    public static function getCurationObjectTypesAsHtml($p_oCurationObjectTypeArray, $p_iRowIndex){
  	  $sReturn = "<select id=\"cboObjectType$p_iRowIndex\" name=\"cboObjectType$p_iRowIndex\"  class=\"spreadsheetInput\" disabled>";
  	  $sReturn = $sReturn . "<option value=''>-Select-</option>";
  	  
  	  /*
  	   * if we have a found curation object type, compare it to
  	   * the values in the list.  if a match, mark the option 
  	   * object as selected.  
  	   */ 
  	  foreach($p_oCurationObjectTypeArray as $oCurationObjectType){
  	    $sCurationObjectType = $oCurationObjectType['OBJECT_TYPE'];
  	    $sReturn = $sReturn . "<option>".$sCurationObjectType."</option>";
  	  }
  	  $sReturn = $sReturn."</select>";
  	  return $sReturn;
    }//end getCurationObjectTypesAsHtml
    
    public static function getHeaders(){
      $sReturn = <<< ENDHTML
        <thead>
          <tr>
            <th>Icon</th>
            <th>Curate</th>
            <th>Done</th>
            <th>Path</th>
            <th>File Name</th>
            <th>Title</th>
            <th>Description</th>
            <th>File Category</th>
            <th>Extension</th>
            <th>Curation Date</th>
            <th>Version</th>
          </tr>
        </thead>
ENDHTML;
	  return $sReturn;      
    }
    
    /**
     * 
     *
     */
    public static function getAjaxHandler($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_sName){
      $sReturn = "";
      if($p_iProjectCurationId <= 0){
        $sReturn = $sReturn . $p_sLabel .": <input type='text' id='$p_sName' name='$p_sName' value='$p_sValue'/>";
      }else{
      	if(!empty($p_sValue)){
          $sReturn = $sReturn . <<< ENDHTML
          $p_sLabel: $p_sValue
          <a href="javascript:void(0);" 
      	     onClick="javascript:getMootools('$p_sEditLink','$p_sResultDivId');">Edit</a>
ENDHTML;
        }else{
      	  $sReturn = $sReturn . <<< ENDHTML
          $p_sLabel: 
          <a href="javascript:void(0);" 
      	     onClick="javascript:getMootools('$p_sEditLink','$p_sResultDivId');">Add</a>
ENDHTML;
        }
      }
      return $sReturn;
    }
    
    /**
     * 
     *
     */
    public static function getAjaxTextAreaHandler($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_sName){
      $sReturn = "";
      if($p_iProjectCurationId <= 0){
        $sReturn = $sReturn . $p_sLabel.":<br><textarea id='$p_sName' name='$p_sName' cols='80' rows='10'>$p_sValue</textarea>";
      }else{
        if(!empty($p_sValue)){
          $sReturn = $sReturn . <<< ENDHTML
          $p_sLabel: 
          <a href="javascript:void(0);" 
      	     onClick="javascript:getMootools('$p_sEditLink','$p_sResultDivId');">Edit</a>
      	  <br>
          $p_sValue   
ENDHTML;
        }else{
      	  $sReturn = $sReturn . <<< ENDHTML
          $p_sLabel: 
          <a href="javascript:void(0);" 
      	     onClick="javascript:getMootools('$p_sEditLink','$p_sResultDivId');">Add</a>
ENDHTML;
        }
      }
      return $sReturn;
    }
    
    /**
     * 
     *
     */
    public static function getHiddenInput($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_sName){
      return $p_sLabel .": ".$p_sValue." <input type='hidden' id='$p_sName' name='$p_sName' value='$p_sValue'/>";
    }
    
    
  }//end class
?>  