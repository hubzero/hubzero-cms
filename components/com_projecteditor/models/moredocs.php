<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/data/Trial.php';
require_once 'lib/data/Repetition.php';

class ProjectEditorModelMoreDocs extends ProjectEditorModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

  /**
   * Finds a flat list of files within a specified directory.  If a user looks
   * for Documentation, the query searches for any path with ../Documentation...
   * @param string $p_strDirectory
   * @param array $p_iHideExperimentIdArray - experiments to exclude
   * @param int $p_iProjectId
   * @param int $p_iLowerLimit
   * @param int $p_iUpperLimit
   * @param int $p_iExperimentId
   * @param int $p_iTrialId
   * @param int $p_iRepetitionId
   * @return array
   */
  public function findDataFileDocumentsByDirectory($p_strDirectory, $p_iHideExperimentIdArray, $p_iProjectId, $p_iLowerLimit=0, $p_iUpperLimit=25, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileDocumentsByDirectory($p_strDirectory, $p_iHideExperimentIdArray, $p_iProjectId,  $p_iLowerLimit, $p_iUpperLimit, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  /**
   * Finds the count of flat list of files within a specified directory.  If a user looks
   * for Documentation, the query searches for any path with ../Documentation...
   * @param string $p_strDirectory
   * @param array $p_iHideExperimentIdArray
   * @param int $p_iProjectId
   * @param int $p_iExperimentId
   * @param int $p_iTrialId
   * @param int $p_iRepetitionId
   * @return int
   */
  public function findDataFileDocumentsByDirectoryCount($p_strDirectory, $p_iHideExperimentIdArray, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileDocumentsByDirectoryCount($p_strDirectory, $p_iHideExperimentIdArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  /**
   * 
   * @param string $p_strDirectory
   * @param int $p_iProjectId
   * @param array $p_iHideExperimentIdArray
   * @return array
   */
  public function findDistinctExperimentsByDirectory($p_strDirectory, $p_iProjectId, $p_iHideExperimentIdArray){
    return DataFileLinkPeer::findDistinctExperimentsByDirectory($p_strDirectory, $p_iProjectId, $p_iHideExperimentIdArray);
  }

  /**
   *
   * @param string $p_strDirectory
   * @param int $p_iProjectId
   * @param int $p_iExperimentId
   * @param array $p_iHideExperimentIdArray
   * @return array
   */
  public function findDistinctTrialsByDirectory($p_strDirectory, $p_iProjectId, $p_iExperimentId, $p_iHideExperimentIdArray){
    return DataFileLinkPeer::findDistinctTrialsByDirectory($p_strDirectory, $p_iProjectId, $p_iExperimentId, $p_iHideExperimentIdArray);
  }

  /**
   *
   * @param <type> $p_strDirectory
   * @param <type> $p_iProjectId
   * @param <type> $p_iExperimentId
   * @param <type> $p_iTrialId
   * @param <type> $p_iHideExperimentIdArray
   * @return <type>
   */
  public static function findDistinctRepetitionsByDirectory($p_strDirectory, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iHideExperimentIdArray){
    return DataFileLinkPeer::findDistinctRepetitionsByDirectory($p_strDirectory, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iHideExperimentIdArray);
  }

  /**
   *
   * @param <type> $p_oExperimentArray
   * @param <type> $p_iExperimentId
   * @return string
   */
  public function findDistinctExperimentsByDirectoryHTML($p_oExperimentArray, $p_iExperimentId=0){
    $strReturn = "Filter:&nbsp;&nbsp; <select id=\"cboExperiment\" name=\"experiment\" onchange=\"onChangeDataTab('frmProject', 'cboTools', 'cboExperiment', 'cboTrial', 'cboRepetition');\">
                                    <option value=0>-Select Experiment-</option>";

    foreach($p_oExperimentArray as $strReturnArray){
      $iExperimentId = $strReturnArray['EXP_ID'];
      $strExperimentName = $strReturnArray['NAME'];
      $strExperimentTitle = $strReturnArray['TITLE'];
      if(strlen($strExperimentTitle) > 40){
        $strExperimentTitle = StringHelper::neat_trim($strExperimentTitle, 40);
      }
      
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

  public function findDistinctTrialsHTML($p_oTrialArray, $p_iTrialId=0){
    $strReturn = "<select id=\"cboTrial\" name=\"trial\" onchange=\"onChangeDataTab('frmProject', 'cboTools', 'cboExperiment', 'cboTrial', 'cboRepetition');\">
                                    <option selected value=0>-Select Trial-</option>";
    foreach($p_oTrialArray as $strReturnArray){
      $iTrialId = $strReturnArray['TRIAL_ID'];
      $strTrialName = $strReturnArray['NAME'];
      $strSelected = "";
      
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

  public function findDistinctRepetitionsHTML($p_oRepetitionArray, $p_iRepetitionId=0){
    $strReturn = "<select id=\"cboRepetition\" name=\"repetition\" onchange=\"onChangeDataTab('frmProject', 'cboTools', 'cboExperiment', 'cboTrial', 'cboRepetition');\">
                                    <option selected value=0>-Select Repetition-</option>";

    foreach($p_oRepetitionArray as $strReturnArray){
      $iRepetitionId = $strReturnArray['REP_ID'];
      $strRepetitionName = $strReturnArray['NAME'];
      $strSelected = "";
      
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

  
}

?>