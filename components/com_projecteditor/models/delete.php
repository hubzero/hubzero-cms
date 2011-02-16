<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.component.view' );

require_once('base.php');
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/Material.php';


class ProjectEditorModelDelete extends ProjectEditorModelBase{

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

  public function deleteGeneratedPicsDataFiles($p_oDataFile){
    DataFilePeer::deleteGeneratedPics($p_oDataFile);
  }

  /**
   *
   * @param Project $p_oProject
   */
  public function deleteProjectGroup($p_oProject){

  }

  /**
   * Displays a table of properties for the given material object.
   * @param Material $p_oMaterial
   * @return string
   */
  public function findMaterialPropertiesByExperimentHTML($p_oMaterial){
    $strPropertiesHTML = "<table style=\"border:0px;margin-left:30px;width:80%\">";

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
          <td nowrap>$strPropertyName:</td>
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
   * Get any files associated with the material.
   * @param Material $p_oMaterial
   * @return string
   */
  public function findMaterialFilesByExperimentHTML($p_oMaterial){
  	$strFilesHTML = "<table style=\"border:0px;margin-left:30px;width:80%\">
  			   <tr><thead><th style='border-bottom:0px;padding-left:0px;'>Title or Name</th><th style='border-bottom:0px;'>Timestamp</th></thead></tr>";

  	foreach($p_oMaterial->getFiles() as $oMaterialFile){
  	  $oFileCreated = $oMaterialFile->getDataFile()->getCreated();
  	  $strFileCreated = strftime("%b %d, %Y - %H:%M", strtotime($oFileCreated));
  	  $strFileName = $oMaterialFile->getDataFile()->getName();
  	  $strFilePath = $oMaterialFile->getDataFile()->getPath();
  	  $strFileTitle = $oMaterialFile->getDataFile()->getTitle();
  	  $strFileDisplay = (strlen($strFileTitle) != 0) ? $strFileTitle : $strFileName;
  	  $strFileLink = $oMaterialFile->getDataFile()->getUrl();  //file to view...

  	  $strFilesHTML .= <<< ENDHTML
	    <tr>
  	      <td nowrap style="padding-left:0px;"><a href="$strFileLink" target="data_file">$strFileDisplay</a></td>
  	      <td>$strFileCreated</td>
  	    <tr>
ENDHTML;
  	}

  	if(sizeof($p_oMaterial->getFiles())==0){
  	  $strFilesHTML .= "<tr><td nowrap style=\"padding-left:0px;\">0 files listed.<td></tr>";
  	}
  	$strFilesHTML .= "</table>";

  	return $strFilesHTML;
  }
}

?>