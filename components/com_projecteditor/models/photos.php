<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('documents.php');


class ProjectEditorModelPhotos extends ProjectEditorModelDocuments {

    /**
     * Constructor
     *
     * @since 1.5
     */
    function __construct() {
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
  public function findDataFilePhotosByDirectory($p_strDirectory, $p_iHideExperimentIdArray, $p_iProjectId, $p_iLowerLimit=0, $p_iUpperLimit=25, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFilePhotosByDirectory($p_strDirectory, $p_iHideExperimentIdArray, $p_iProjectId,  $p_iLowerLimit, $p_iUpperLimit, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
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
  public function findDataFilePhotosByDirectoryCount($p_strDirectory, $p_iHideExperimentIdArray, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFilePhotosByDirectoryCount($p_strDirectory, $p_iHideExperimentIdArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  /**
   *
   * @param int $p_iPhotoType
   * @param int $p_iProjectId
   * @param int $p_iExperimentId
   * @param int $p_iLowerLimit
   * @param int $p_iUpperLimit
   * @return array
   */
  public function getProjectEditorPhotos($p_iPhotoType, $p_iProjectId, $p_iExperimentId, $p_iLowerLimit, $p_iUpperLimit){
    return DataFilePeer::getProjectEditorPhotos($p_iPhotoType, $p_iProjectId, $p_iExperimentId, $p_iLowerLimit, $p_iUpperLimit);
  }

  /**
   * 
   * @param int $p_iPhotoType
   * @param int $p_iProjectId
   * @param int $p_iExperimentId
   * @return int
   */
  public function getProjectEditorPhotosCount($p_iPhotoType, $p_iProjectId, $p_iExperimentId){
    return DataFilePeer::getProjectEditorPhotosCount($p_iPhotoType, $p_iProjectId, $p_iExperimentId);
  }
    
}
?>