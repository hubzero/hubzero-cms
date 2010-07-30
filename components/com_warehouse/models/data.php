<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'lib/data/DataFilePeer.php';

class WarehouseModelData extends WarehouseModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }
  
  /**
   * Look inside the data_file_link table by trial-id and 
   * rep-id equal to 0.  If we get results, these files 
   * are on the trial level.  
   * 
   * @param $p_iMembersId
   */
  public function findDataByTrial($p_iTrialId){
    return DataFilePeer::getDataFilesByTrial($p_iTrialId);
  }
  
  /**
   * Look inside the data_file_link table by trial-id and 
   * rep-id equal to 0.  If we get results, these files 
   * are on the trial level.  
   * 
   * @param $p_iMembersId
   */
  public function getDataFilesByTrialIdAndPath($p_iTrialId, $p_strPathEndsWith){
    return DataFilePeer::getDataFilesByTrialIdAndPath($p_iTrialId, $p_strPathEndsWith);
  }
  
  /**
   * Look inside the data_file_link table where rep-id 
   * equals the given parameter.  If we get results, 
   * the data files are on the repetition level.
   * 
   * @param $p_iRepetitionId
   */
  public function findDataByRepetition($p_iRepetitionId){
    return DataFilePeer::getDataFilesByRepetition($p_iRepetitionId);
  }
  
  public function findByDirectory($p_strPath){
    return DataFilePeer::findByDirectory($p_strPath);
  }
  
  public function findDataFileOpeningTools($p_strName, $p_iLowerLimit=1, $p_iUpperLimit=25, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileOpeningTools($p_strName, $p_iLowerLimit, $p_iUpperLimit, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileOpeningToolsCount($p_strName, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileOpeningToolsCount($p_strName, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileOpeningToolsHTML($p_strDataFileArray){
    $strReturn = "<table summary='A list of tool files.' id='fileList'>
                                    <thead>
                                            <tr>
                                                    <th width='70%'>Launch</th>
                                                    <th width='15%'>Experiment</th>
                                                    <th width='15%'>Trial</th>
                                                    <th width='15%'>Repetition</th>
                                            </tr>
                                    </thead>";

    foreach($p_strDataFileArray as $iFileIndex=>$strDataFile){
      $strRow = "odd";
      if($iFileIndex%2==0){
        $strRow = "even";
      }

      $strPath = $strDataFile['PATH'];
      $strName = $strDataFile['NAME'];
      $strTitle = $strDataFile['TITLE'];
      $strDescription = $strDataFile['DESCRIPTION'];
      $iProjectId = $strDataFile['PROJ_ID'];
      $iExperimentId = $strDataFile['EXP_ID'];
      $iTrialId = $strDataFile['TRIAL_ID'];
      $iRepId = $strDataFile['REP_ID'];

      $strExperimentTitle = (isset($strDataFile['E_TITLE'])) ? $strDataFile['E_TITLE'] : "";
      $strExperimentName = (isset($strDataFile['E_NAME'])) ? $strDataFile['E_NAME'] : "";
      $strExperimentNameDisplay = $strExperimentName;

      $strTrialName = (isset($strDataFile['T_NAME'])) ? $strDataFile['T_NAME'] : "";
      $strRepetitionName = (isset($strDataFile['R_NAME'])) ? $strDataFile['R_NAME'] : "";

      $strDisplay = (strlen($strTitle)==0) ? $strName : $strTitle;
      $strDescription = (strlen($strDescription)!=0) ? $strDescription : "Description not available";
      
      $strInDEEDPath = InDEED::LAUNCH;

      $strReturn .= <<< ENDHTML
              <tr class="$strRow">
                    <td><a href="$strInDEEDPath?list=$strPath/$strName" class="Tips3" title="$strDisplay :: $strDescription">$strDisplay</a></td>
                    <td><a href="/warehouse/experiment/$iExperimentId/project/$iProjectId" class="Tips3" title="$strExperimentName :: $strExperimentTitle">$strExperimentNameDisplay</a></td>
                    <td>$strTrialName</td>
                    <td>$strRepetitionName</td>
              </tr>
ENDHTML;
    }

    if(empty($p_strDataFileArray)){
      $strReturn .= "<tr>
                      <td colspan='4'><p class='warning'>No tools found.</p></td>
                    </tr>";
    }

    $strReturn .= "</table>";
    return $strReturn;
  }
  
  public function findDataFileByUsage($p_strUsage, $p_iLowerLimit=1, $p_iUpperLimit=25, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileByUsage($p_strUsage, $p_iLowerLimit, $p_iUpperLimit, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileByUsageCount($p_strUsage, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileByUsageCount($p_strUsage, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileByUsageHTML($p_strDataFileArray){
    $strReturn = "<table summary='A list of drawing files.' id='fileList'>
                                    <thead>
                                            <tr>
                                                    <th width='90%'>Drawing</th>
                                                    <th width='10%'>Experiment</th>
                                            </tr>
                                    </thead>";

    foreach($p_strDataFileArray as $iFileIndex=>$strDataFile){
      $strRow = "odd";
      if($iFileIndex%2==0){
        $strRow = "even";
      }

      $strPath = $strDataFile['PATH'];
      $strName = $strDataFile['NAME'];
      $strTitle = $strDataFile['TITLE'];
      $strDescription = $strDataFile['DESCRIPTION'];
      $iProjectId = $strDataFile['PROJ_ID'];
      $iExperimentId = $strDataFile['EXP_ID'];
      $iTrialId = $strDataFile['TRIAL_ID'];
      $iRepId = $strDataFile['REP_ID'];

      $strExperimentTitle = (isset($strDataFile['E_TITLE'])) ? $strDataFile['E_TITLE'] : "";
      $strExperimentName = (isset($strDataFile['E_NAME'])) ? $strDataFile['E_NAME'] : "";
      $strExperimentNameDisplay = $strExperimentName;

      $strTrialName = (isset($strDataFile['T_NAME'])) ? $strDataFile['T_NAME'] : "";
      $strTrialNameArray = explode("-",$strTrialName);
      $strTrialName = (sizeof($strTrialNameArray)==2) ? $strTrialNameArray[1] : $strDataFile['T_NAME'];

      $strRepetitionName = (isset($strDataFile['R_NAME'])) ? $strDataFile['R_NAME'] : "";
      $strRepetitionNameArray = explode("-",$strRepetitionName);
      $strRepetitionName = (sizeof($strRepetitionNameArray)==2) ? $strRepetitionNameArray[1] : $strDataFile['R_NAME'];

      $strDisplay = (strlen($strTitle)==0) ? $strName : $strTitle;
      $strDescription = (strlen($strDescription)!=0) ? $strDescription : "Description not available";

      $strDrawingUrl = $strPath."/".$strName;
      $strDrawingUrl = str_replace("/nees/home/",  "",  $strDrawingUrl);
      $strDrawingUrl = str_replace(".groups",  "",  $strDrawingUrl);

      $strLightbox = "";
      $strExtension = $strDataFile["EXTENSION"];
      if($strExtension==="png" || $strExtension==="jpg" || $strExtension==="gif"){
        $strLightbox = "lightbox[drawings]";
      }

      $strReturn .= <<< ENDHTML
              <tr class="$strRow">
                    <td><a rel="$strLightbox" href="/data/get/$strDrawingUrl" title="$strDescription">$strDisplay</a></td>
                    <td><a href="/warehouse/experiment/$iExperimentId/project/$iProjectId" class="Tips3" title="$strExperimentName :: $strExperimentTitle">$strExperimentNameDisplay</a></td>
              </tr>
ENDHTML;
    }

    if(empty($p_strDataFileArray)){
      $strReturn .= "<tr>
                      <td colspan='2'><p class='warning'>No drawings found.</p></td>
                    </tr>";
    }

    $strReturn .= "</table>";
    return $strReturn;
  }
  
  public function findDistinctExperiments($p_iProjectId, $p_strOpeningTool="", $p_strUsageType=""){
    return DataFileLinkPeer::findDistinctExperiments($p_iProjectId, $p_strOpeningTool, $p_strUsageType);
  }
  
  public function findDistinctExperimentsHTML($p_oExperimentArray, $p_iProjectId, $p_iExperimentId=0){
    $strReturn = "Filter:&nbsp;&nbsp; <select id=\"cboExperiment\" name=\"experiment\" onchange=\"document.getElementById('frmData').submit();\">
                                    <option value=0>-Select Experiment-</option>";

    foreach($p_oExperimentArray as $strReturnArray){
      $iExperimentId = $strReturnArray['EXP_ID'];
      $strExperimentName = $strReturnArray['NAME'];
      //echo "select $iExperimentId vs. $p_iExperimentId<br>";
      $strSelected = "";
      if($iExperimentId==$p_iExperimentId){
        $strSelected = "selected";
      }
      $strReturn .= <<< ENDHTML
              <option value="$iExperimentId" $strSelected>$strExperimentName</option>
ENDHTML;
    }
    $strReturn .= "</select>";
    return $strReturn;
  }
  
  public function findDistinctTrials($p_iProjectId, $p_iExperimentId, $p_strOpeningTool="", $p_strUsageType=""){
  	return DataFileLinkPeer::findDistinctTrials($p_iProjectId, $p_iExperimentId, $p_strOpeningTool, $p_strUsageType);
  }
  
  public function findDistinctTrialsHTML($p_oTrialArray, $p_iProjectId, $p_iTrialId){
    $strReturn = "<select id=\"cboTrial\" name=\"trial\" onchange=\"document.getElementById('frmData').submit();\">
                                    <option selected value=0>-Select Trial-</option>";
    foreach($p_oTrialArray as $strReturnArray){
      $iTrialId = $strReturnArray['TRIAL_ID'];
      $strTrialName = $strReturnArray['NAME'];
      $strSelected = "";
      //echo "select $iTrialId, $p_iTrialId<br>";
      if($iTrialId==$p_iTrialId){
        $strSelected = "selected";
      }
      $strReturn .= <<< ENDHTML
              <option value="$iTrialId" $strSelected>$strTrialName</option>
ENDHTML;
    }
    $strReturn .= "</select>";
    return $strReturn;
  }
  
  public function findDistinctRepetitions($p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_strOpeningTool="", $p_strUsageType=""){
    return DataFileLinkPeer::findDistinctRepetitions($p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_strOpeningTool, $p_strUsageType);
  }
  
  public function findDistinctRepetitionsHTML($p_oRepetitionArray, $p_iRepetitionId=0){
    $strReturn = "<select id=\"cboRepetition\" name=\"repetition\" onchange=\"document.getElementById('frmData').submit();\">
                                    <option selected value=0>-Select Repetition-</option>";

    foreach($p_oRepetitionArray as $strReturnArray){
      $iRepetitionId = $strReturnArray['REP_ID'];
      $strRepetitionName = $strReturnArray['NAME'];
      $strSelected = "";
      //echo "select $iRepetitionId, $p_iRepetitionId<br>";
      if($iRepetitionId==$p_iRepetitionId){
        $strSelected = "selected";
      }

      $strReturn .= <<< ENDHTML
              <option value="$iRepetitionId" $strSelected>$strRepetitionName</option>
ENDHTML;
    }
    $strReturn .= "</select>";
    return $strReturn;
  }
  
  public function findDistinctOpeningTools($p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDistinctOpeningTools($p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDistinctOpeningToolsHTML($p_strToolArray, $p_strTool=""){
    $strReturn = "Tools:&nbsp;&nbsp; <select id=\"cboTools\" name=\"tool\" onchange=\"document.getElementById('frmData').submit();\">
                                    <option>-Select Tool-</option>";

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
  
  public function findPhotoDataFiles($p_strExcludeUsageArray, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0, $p_iLowerLimit=0, $p_iUpperLimit=24){
    return DataFilePeer::findPhotoDataFiles($p_strExcludeUsageArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId, $p_iLowerLimit, $p_iUpperLimit);	
  }
  
  public static function findPhotoDataFilesCount($p_strExcludeUsageArray, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findPhotoDataFilesCount($p_strExcludeUsageArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findPhotoDataFilesHTML($p_oPhotoDataFileArray){
    $strReturn = "<div id='images' style='padding-top:3em;'>
                                <table style='width:100%;border-top:0px;border-bottom:0px;'>
                                      <tr>";

    foreach($p_oPhotoDataFileArray as $iFileIndex=>$oPhotoDataFile){
      $iPhotoCounter = $iFileIndex + 1;

      $strThumbnail = $oPhotoDataFile['THUMBNAIL'];
      $strCloseRow = "";
      $strOpenRow = "";
      if($iFileIndex>0 && $iPhotoCounter%4===0){
        $strCloseRow = "</tr>";
            if($iFileIndex < sizeof($p_oPhotoDataFileArray)){
              $strOpenRow = "<tr>";
            }
      }

      $strReturn .= <<< ENDHTML
              <td align="center" style="float:left; height:100px; width:20%; margin-bottom:50px;">$strThumbnail</td>$strCloseRow
              $strOpenRow
ENDHTML;
    }

    if(empty($p_oPhotoDataFileArray)){
      $strReturn .= "<p class='warning'>No images found.</p> ";
    }

    $strReturn .=  "  </tr>
                                 </table>
                                </div>";

    return $strReturn;
  }

}

?>