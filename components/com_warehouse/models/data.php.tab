<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'neesconfiguration.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/DataFileLink.php';

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
  
  public function findDataFileOpeningTools($p_strName, $p_oHideExperimentArray, $p_iLowerLimit=1, $p_iUpperLimit=25, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileOpeningTools($p_strName, $p_oHideExperimentArray, $p_iLowerLimit, $p_iUpperLimit, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileOpeningToolsCount($p_strName, $p_oHideExperimentArray, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileOpeningToolsCount($p_strName, $p_oHideExperimentArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileOpeningToolsHTML($p_strDataFileArray){
    $strReturn = "<table summary='A list of tool files.' id='fileList'>
                                    <thead>
                                            <tr>
                                                    <th nowrap>Launch</th>
                                                    <th>Experiment</th>
                                                    <th>Trial</th>
                                                    <th>Repetition</th>
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
      //$strExperimentNameDisplay = $strExperimentName;
      $strExperimentNameDisplay = $strExperimentTitle;

      $strTrialName = (isset($strDataFile['T_NAME'])) ? $strDataFile['T_NAME'] : "";
      $strRepetitionName = (isset($strDataFile['R_NAME'])) ? $strDataFile['R_NAME'] : "";

      $strDisplay = (strlen($strTitle)==0) ? $strName : $strTitle;
      //$strDescription = (strlen($strDescription)!=0) ? $strDescription : "Description not available";

      $strTooltip = (StringHelper::hasText($strDescription)) ? $strDescription : "";
      $strTooltip .= (StringHelper::hasText($strTooltip)) ? " ::: $strPath" : $strPath;

      $strLaunchInEED = NeesConfig::LAUNCH_INDEED;

      $strReturn .= <<< ENDHTML
              <tr class="$strRow">
                    <td nowrap><a href="$strLaunchInEED=$strPath/$strName" title="$strTooltip">$strDisplay</a></td>
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

  public function findDataFiles($p_oHideExperimentArray, $p_iLowerLimit=1, $p_iUpperLimit=25, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFiles($p_oHideExperimentArray, $p_iLowerLimit, $p_iUpperLimit, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  public function findDataFilesCount($p_oHideExperimentArray, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFilesCount($p_oHideExperimentArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  public function findDataFilesHTML($p_strDataFileArray){
    $strReturn = "<table summary='A list of tool files.' id='fileList'>
                                    <thead>
                                            <tr>
                                                    <th nowrap>Launch</th>
                                                    <th>Experiment</th>
                                                    <th>Trial</th>
                                                    <th>Repetition</th>
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
      //$strExperimentNameDisplay = $strExperimentName;
      $strExperimentNameDisplay = $strExperimentTitle;

      $strTrialName = (isset($strDataFile['T_NAME'])) ? $strDataFile['T_NAME'] : "";
      $strRepetitionName = (isset($strDataFile['R_NAME'])) ? $strDataFile['R_NAME'] : "";

      $strDisplay = (strlen($strTitle)==0) ? $strName : $strTitle;
      //$strDescription = (strlen($strDescription)!=0) ? $strDescription : "Description not available";

      $strTooltip = (StringHelper::hasText($strDescription)) ? $strDescription : "";
      $strTooltip .= (StringHelper::hasText($strTooltip)) ? " ::: $strPath" : $strPath;

      $strReturn .= <<< ENDHTML
              <tr class="$strRow">
                    <td><a href="/data/get$strPath/$strName" title="$strTooltip" target="neesProjectData">$strDisplay</a></td>
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
  
  public function findDataFileByUsage($p_strUsage, $p_oHideExperimentArray, $p_iLowerLimit=1, $p_iUpperLimit=25, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileByUsage($p_strUsage, $p_oHideExperimentArray, $p_iLowerLimit, $p_iUpperLimit, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileByUsageCount($p_strUsage, $p_oHideExperimentArray, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileByUsageCount($p_strUsage, $p_oHideExperimentArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  public function findDataFileByUsageHTML($p_oDataFileArray){
    $strReturn = "<table summary='A list of drawing files.' id='fileList'>
                                    <thead>
                                            <tr>
                                                    <th nowrap>Drawing</th>
                                                    <th>Experiment</th>
                                            </tr>
                                    </thead>";

    /* @var $oDataFile DataFile */
    foreach($p_oDataFileArray as $iFileIndex=>$oDataFile){
      $strRow = "odd";
      if($iFileIndex%2==0){
        $strRow = "even";
      }

      $iDataFileId = $oDataFile->getId();
      $strPath = $oDataFile->getPath();
      $strName = $oDataFile->getName();
      $strTitle = $oDataFile->getTitle();
      $strDescription = $oDataFile->getDescription();

      /* @var $oDataFileLink DataFileLink */
      $oDataFileLink = DataFileLinkPeer::retrieveByPK($iDataFileId);
      $iProjectId = $oDataFileLink->getProject()->getId();
      $iExperimentId = $oDataFileLink->getExperimentId();
      $iTrialId = $oDataFileLink->getTrialId();
      $iRepId = $oDataFileLink->getRepId();

      $strExperimentTitle = ($iExperimentId > 0) ? $oDataFileLink->getExperiment()->getTitle() : "";
      $strExperimentName = ($iExperimentId > 0) ? $oDataFileLink->getExperiment()->getName() : "";
      //$strExperimentNameDisplay = $strExperimentName;
      $strExperimentNameDisplay = $strExperimentTitle;

      $strTrialName = ($iTrialId > 0) ? $oDataFileLink->getTrial()->getName() : "";
      $strTrialNameArray = explode("-",$strTrialName);
      $strTrialName = (sizeof($strTrialNameArray)==2) ? $strTrialNameArray[1] : "";

      $strRepetitionName = ($iRepId > 0) ? $oDataFileLink->getRepetition()->getName() : "";
      $strRepetitionNameArray = explode("-",$strRepetitionName);
      $strRepetitionName = (sizeof($strRepetitionNameArray)==2) ? $strRepetitionNameArray[1] : "";

      $strDisplay = (strlen($strTitle)==0) ? $strName : $strTitle;
      $strDescription = (strlen($strDescription)!=0) ? $strDescription : "Description not available";

      $strDrawingUrl = $strPath."/display_".$iDataFileId."_".$strName;
      $strDrawingUrl = str_replace("/nees/home/",  "",  $strDrawingUrl);
      $strDrawingUrl = str_replace(".groups",  "",  $strDrawingUrl);

      $strLightbox = "";
      $strNameArray = explode(".", $strName);
      $strExtension = $strNameArray[1];
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

    if(empty($p_oDataFileArray)){
      $strReturn .= "<tr>
                      <td colspan='2'><p class='warning'>No drawings found.</p></td>
                    </tr>";
    }

    $strReturn .= "</table>";
    return $strReturn;
  }
  
  public function findDistinctExperiments($p_iProjectId, $p_oHideExperimentArray, $p_strOpeningTool="", $p_strUsageType=""){
    return DataFileLinkPeer::findDistinctExperiments($p_iProjectId, $p_oHideExperimentArray, $p_strOpeningTool, $p_strUsageType);
  }

  public function findDistinctExperimentsHTML($p_oExperimentArray, $p_iProjectId, $p_iExperimentId=0){
    $strReturn = "Filter:&nbsp;&nbsp; <select id=\"cboExperiment\" name=\"experiment\" onchange=\"onChangeDataTab('frmData', 'cboTools', 'cboExperiment', 'cboTrial', 'cboRepetition');\">
                                    <option value=0>-Select Experiment-</option>";

    foreach($p_oExperimentArray as $strReturnArray){
      $iExperimentId = $strReturnArray['EXP_ID'];
      $strExperimentName = $strReturnArray['NAME'];
      $strExperimentTitle = $strReturnArray['TITLE'];
      if(strlen($strExperimentTitle) > 40){
        $strExperimentTitle = StringHelper::neat_trim($strExperimentTitle, 40);
      }

      //echo "select $iExperimentId vs. $p_iExperimentId<br>";
      $strSelected = "";
      if($iExperimentId==$p_iExperimentId){
        $strSelected = "selected";
      }
      $strReturn .= <<< ENDHTML
              <option value="$iExperimentId" $strSelected>$strExperimentTitle</option>
ENDHTML;
    }
    $strReturn .= "</select>";
    return $strReturn;
  }
  
  public function findDistinctTrials($p_oHideExperimentArray, $p_iProjectId, $p_iExperimentId, $p_strOpeningTool="", $p_strUsageType=""){
  	return DataFileLinkPeer::findDistinctTrials($p_oHideExperimentArray, $p_iProjectId, $p_iExperimentId, $p_strOpeningTool, $p_strUsageType);
  }

  public function findDistinctTrialsHTML($p_oTrialArray, $p_iProjectId, $p_iTrialId){
    $strReturn = "<select id=\"cboTrial\" name=\"trial\" onchange=\"onChangeDataTab('frmData', 'cboTools', 'cboExperiment', 'cboTrial', 'cboRepetition');\">
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
  
  public function findDistinctRepetitions($p_oHideExperimentArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_strOpeningTool="", $p_strUsageType=""){
    return DataFileLinkPeer::findDistinctRepetitions($p_oHideExperimentArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_strOpeningTool, $p_strUsageType);
  }

  public function findDistinctRepetitionsHTML($p_oRepetitionArray, $p_iRepetitionId=0){
    $strReturn = "<select id=\"cboRepetition\" name=\"repetition\" onchange=\"onChangeDataTab('frmData', 'cboTools', 'cboExperiment', 'cboTrial', 'cboRepetition');\">
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
  
  public function findDistinctOpeningToolsHTML0($p_strToolArray, $p_strTool=""){
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

  public function findDistinctOpeningToolsHTML($p_strToolArray, $p_strTool=""){
    $strReturn = "Tools:&nbsp;&nbsp; <select id=\"cboTools\" name=\"tool\" onchange=\"onChangeDataTab('frmData', 'cboTools', 'cboExperiment', 'cboTrial', 'cboRepetition');\">
                                    <option value=''>-Select Tool-</option>";

    array_push($p_strToolArray, "Any");
    
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
  
  public function findPhotoDataFiles($p_oHideExperimentArray, $p_strExcludeUsageArray, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0, $p_iLowerLimit=0, $p_iUpperLimit=24){
    return DataFilePeer::findPhotoDataFiles($p_oHideExperimentArray, $p_strExcludeUsageArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId, $p_iLowerLimit, $p_iUpperLimit);
  }
  
  public static function findPhotoDataFilesCount($p_oHideExperimentArray, $p_strExcludeUsageArray, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findPhotoDataFilesCount($p_oHideExperimentArray, $p_strExcludeUsageArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
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