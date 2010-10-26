<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'lib/data/MaterialPeer.php';

class WarehouseModelMaterials extends WarehouseModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  /**
   * Look up the materials by experimentId
   * @param $p_iExpid - experiment identifier
   * @return array of Material objects
   */
  public function findMaterialsByExperiment($p_iExpid){
  	return MaterialPeer::findByExperiment($p_iExpid);
  }
  
  /**
   * Return an html table of materials for the current experiment.
   * @param $p_oMaterialArray - array of materials
   * @param $p_iProjectId - project identifier
   * @param $p_iExperimentId - experiment identifier
   * @param $p_iSelectedMaterialId - a selected material
   * @returns html string
   */
  public function findMaterialsByExperimentHTML($p_oMaterialArray, $p_iProjectId, $p_iExperimentId, $p_iSelectedMaterialId=0){
  	$strHTML = "<table>
  				  <tr>
  				    <thead>
  				      <th style=\"width:16px\"></th>
  				      <th>Name</th>
  				      <th>Type</th>
  				    </thead>
  				  <tr>";
  	
  	foreach($p_oMaterialArray as $iMaterialIndex=>$oMaterial){
  	  $iMaterialId = $oMaterial->getId();
  	  $strMaterialName = $oMaterial->getName();
  	  $strMaterialDesc = $oMaterial->getDescription();
  	  $strMaterialType = $oMaterial->getMaterialType()->getName();
  	  
  	  //get the properties table
  	  $strPropertiesHTML = $this->findMaterialPropertiesByExperimentHTML($oMaterial);

  	  //get the files table
  	  $strFilesHTML = $this->findMaterialFilesByExperimentHTML($oMaterial);
  	  
  	  //set the background color
  	  $strBgColor = "odd";
  	  if($iMaterialIndex %2 == 0){
  	    $strBgColor = "even";	
  	  }
  	  
  	  if($iMaterialId==$p_iSelectedMaterialId){
  	    $strHTML .= <<< ENDHTML
	      <tr class="$strBgColor">
  	        <td><a style="border:0px;" href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/$p_iProjectId/experiment/$p_iExperimentId?format=ajax', 'experimentInfo');"><img border="0" width="16" height="16" alt="" title="Collapse" src="/components/com_warehouse/images/arrow_down.png" style="border: 0px none;"></a></td>
  	        <td>
  	          <a href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/$p_iProjectId/experiment/$p_iExperimentId?format=ajax', 'experimentInfo');">$strMaterialName</a>
  	          <p>$strMaterialDesc</p>
  	          <p><b>Properties:</b></p>
  	          $strPropertiesHTML
  	          <p><b>Files:</b></p>
  	          $strFilesHTML
  	        </td>
  	        <td>$strMaterialType</td>
  	      <tr>	            
ENDHTML;
  	  }else{
  	  	$strHTML .= <<< ENDHTML
	      <tr class="$strBgColor">
  	        <td><a style="border:0px;" href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/$p_iProjectId/experiment/$p_iExperimentId/detail/$iMaterialId?format=ajax', 'experimentInfo');"><img border="0" width="16" height="16" alt="" title="Expand" src="/components/com_warehouse/images/arrow_right.png" style="border: 0px none;"></a></td>
  	        <td><a href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/$p_iProjectId/experiment/$p_iExperimentId/detail/$iMaterialId?format=ajax', 'experimentInfo');">$strMaterialName</a></td>
  	        <td>$strMaterialType</td>
  	      <tr>	            
ENDHTML;
  	  }
  	}

    $strHTML .= "</table>";
    
    return $strHTML;
  }
  
  /**
   * Displays a table of properties for the given material object.
   * @param $p_oMaterial - current material object. 
   * @returns html string
   */
  public function findMaterialPropertiesByExperimentHTML($p_oMaterial){
    $strPropertiesHTML = "<table style=\"border-bottom:0px;border-top:0px;margin-left:30px;\">";

    foreach($p_oMaterial->getMaterialProperties() as $oMaterialProperty){
      $strPropertyName = $oMaterialProperty->getMaterialTypeProperty()->getName();
      $strPropertyValue = $oMaterialProperty->getValue();
      $strUnitName = "";
      $strUnitAbbreviation = "";
      $strUnits = "";

      if( $oMaterialProperty->getUnit() ) {
        if( $oMaterialProperty->getUnit()->getCategory()->getName() == 'Time' ) {
          $strUnitName = $oMaterialProperty->getUnit()->getName();
        } else {
          $strUnitAbbreviation = $oMaterialProperty->getUnit()->getAbbreviation();
        }
      } else {
        $strUnits = $oMaterialProperty->getMaterialTypeProperty()->getUnits();
      }

      $strPropertiesHTML .= <<< ENDHTML
        <tr>
          <td nowrap width="1">$strPropertyName:</td>
          <td>$strPropertyValue $strUnitName $strUnitAbbreviation $strUnits</td>
        <tr>
ENDHTML;
    }

    if(sizeof($p_oMaterial->getMaterialProperties())==0){
      $strPropertiesHTML .= "<tr><td>No properties listed.<td></tr>";
    }
    $strPropertiesHTML .= "</table>";

    return $strPropertiesHTML;
  }

  /**
   * Displays a table of files for the given material object.
   * @param $p_oMaterial - current material object. 
   * @returns html string
   */
  public function findMaterialFilesByExperimentHTML($p_oMaterial){
  	$strFilesHTML = "<table style=\"border-bottom:0px;border-top:0px;margin-left:30px;\">
  					  <tr><thead><th style='border-bottom:0px;'>Title or Name</th><th style='border-bottom:0px;'>Timestamp</th></thead></tr>";
  	
  	foreach($p_oMaterial->getFiles() as $oMaterialFile){
  	  $oFileCreated = $oMaterialFile->getDataFile()->getCreated();
  	  $strFileCreated = strftime("%b %d, %Y - %H:%M", strtotime($oFileCreated));
  	  $strFileName = $oMaterialFile->getDataFile()->getName();
  	  $strFilePath = $oMaterialFile->getDataFile()->getPath();
  	  $strFileTitle = $oMaterialFile->getDataFile()->getTitle();
  	  $strFileDisplay = (strlen($strFileTitle) != 0) ? $strFileTitle : $strFileName;
  	  $strFileLink = $oMaterialFile->getDataFile()->get_url();  //file to view...
  	  	
  	  $strFilesHTML .= <<< ENDHTML
	    <tr>
  	      <td nowrap><a href="$strFileLink" target="data_file">$strFileDisplay</a></td>
  	      <td>$strFileCreated</td>
  	    <tr>	            
ENDHTML;
  	}
  	
  	if(sizeof($p_oMaterial->getFiles())==0){
  	  $strFilesHTML .= "<tr><td>No files listed.<td></tr>";
  	}
  	$strFilesHTML .= "</table>";
  	  
  	return $strFilesHTML;
  }
  
  /**
   * Gets the material by primary key.
   * @param $p_iMaterialId
   * @returns Material
   */
  public function findMaterialById($p_iMaterialId){
  	return MaterialPeer::find($p_iMaterialId);
  }
  
  /**
   * 
   *
   */
  public function getTrialById($p_iTrialId){
  	return TrialPeer::retrieveByPK($p_iTrialId);
  }
}

?>