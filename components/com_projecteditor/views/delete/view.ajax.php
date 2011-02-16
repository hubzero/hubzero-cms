<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class ProjectEditorViewDelete extends JView{

  function display($tpl = null){
    $oAuthorizer = Authorizer::getInstance();

    /* @var $oModel ProjectEditorModelDelete */
    $oModel =& $this->getModel();

    //incoming
    $iEntityId = JRequest::getInt('eid',0);
    $iEntityTypeId = JRequest::getInt('etid',0);
    $strPath = JRequest::getVar("path", "");
    $strReturnUrl = JRequest::getString("return", "");

    /* @var $oEntityType EntityType */
    $oEntityType = $oModel->getEntityTypeById($iEntityTypeId);
    if(!$oEntityType){
      echo ComponentHtml::showError("Please provide an entity type.");
      return;
    }

    $bCanDelete = false;
    $bEntityFound = true;
    $strName = StringHelper::EMPTY_STRING;
    $strTitle = StringHelper::EMPTY_STRING;
    $strDescription = StringHelper::EMPTY_STRING;
    $oHideExperimentArray = array();
    $oDataFileArray = array();
    $iDataFileCount = 0;

    $strClassName = $oEntityType->getClassName();
    switch ($strClassName) {
        case "Project":
            /* @var $oProject Project */
            $oProject = $oModel->getProjectById($iEntityId);
            if(!$oProject){
              echo "<div><h2>Unable to Delete</h2></div>";  
              echo ComponentHtml::showError("Project($iEntityId) not found.");
              return;
            }

            $strName = $oProject->getName();
            $strTitle = $oProject->getTitle();
            $strDescription = $oProject->getDescription();

            $oExperimentArray = array();
            $oExperimentTempArray = $oProject->getExperiments();
            foreach($oExperimentTempArray as $oExperiment){
              /* @var $oExperiment Experiment */
              if($oExperiment->getDataFileLinkCount(0) > 0){
                array_push($oExperimentArray, $oExperiment);
              }
            }
            $_REQUEST[ExperimentPeer::TABLE_NAME] = serialize($oExperimentArray);

            $this->assignRef("iProjectFiles", $oProject->getDataFileLinkCount(0));

            $oProjectDataFileArray = DataFilePeer::findDataFilesByPath($oProject->getPathname(), 1, 25, $oProject->getId(), 0, 0, 0, true);
            $_REQUEST["ProjectFiles"] = serialize($oProjectDataFileArray);

            //if dropbox exists, read files and dirs at parent level
            $strProjectName = $oProject->getName();
            $strProjectName = str_replace("-", "_", $strProjectName);
            $strDropboxDirectory = ProjectEditor::DROPBOX_PREFIX."/".strtolower($strProjectName)."/".ProjectEditor::DROPBOX_SUFFIX;

            $strDropboxFileArray = array();
            if(is_dir($strDropboxDirectory)){
              $strDropboxFileArray = FileHelper::readDir($strDropboxDirectory);
            }
            $this->assignRef("strDropBoxArray", $strDropboxFileArray);

            $this->setLayout("project");
            break;
        case "Experiment":
            /* @var $oExperiment Experiment */
            $oExperiment = $oModel->getExperimentById($iEntityId);
            if(!$oExperiment){
              echo "<div><h2>Unable to Delete</h2></div>";
              echo ComponentHtml::showError("Experiment($iEntityId) not found.");
              return;
            }

            $strName = $oExperiment->getName();
            $strTitle = $oExperiment->getTitle();
            $strDescription = $oModel->getDisplayDescription($oExperiment->getDescription());

            $oTrialArray = array();
            $oTrialTempArray = $oExperiment->getTrials();
            foreach($oTrialTempArray as $oTrial){
              /* @var $oTrial Trial */
              if($oTrial->getDataFileLinkCount(0) > 0){
                array_push($oTrialArray, $oTrial);
              }
            }
            $_REQUEST[TrialPeer::TABLE_NAME] = serialize($oTrialArray);

            $this->assignRef("iExperimentFiles", $oExperiment->getDataFileLinkCount(0));

            $oExperimentDataFileArray = DataFilePeer::findDataFilesByPath($oExperiment->getPathname(), 1, 25, $oExperiment->getProjectId(), $oExperiment->getId(), 0, 0, true);
            $_REQUEST["ExperimentFiles"] = serialize($oExperimentDataFileArray);

            $this->setLayout("experiment");

            break;
        case "Trial":
            $strEntityIds = JRequest::getVar('eid');
            if(!StringHelper::hasText($strEntityIds)){
              echo "<div><h2>Unable to Delete</h2></div>";
              echo ComponentHtml::showError("Please select at least 1 trial.");
              return;
            }

            $strEntityIdArray = explode(",", $strEntityIds);
            if(count($strEntityIdArray) > 1){
              $oTrialArray = TrialPeer::retrieveByPKs($strEntityIdArray);
              $_REQUEST[TrialPeer::TABLE_NAME] = serialize($oTrialArray);

              $iDataFileCount = count($oTrialArray);

              //overwrite the single entity id with the list.
              $iEntityId = $strEntityIds;

              $this->setLayout("trial_list");
            }else{
              /* @var $oTrial Trial */
              $oTrial = $oModel->getTrialById($iEntityId);
              if(!$oTrial){
                echo ComponentHtml::showError("Trial($iEntityId) not found.");
                return;
              }

              $strName = $oTrial->getName();
              $strTitle = $oTrial->getTitle();
              $strDescription = $oTrial->getDescription();

              $oRepetitionArray = array();
              $oRepetitionTempArray = $oTrial->getRepetitions();
              foreach($oRepetitionTempArray as $oRepetition){
                /* @var $oRepetition Repetition */
                if($oRepetition->getDataFileLinkCount(0) > 0){
                  array_push($oRepetitionArray, $oRepetition);
                }
              }
              $_REQUEST[RepetitionPeer::TABLE_NAME] = serialize($oRepetitionArray);

              $this->assignRef("iTrialFiles", $oTrial->getDataFileLinkCount(0));

              $oTrialDataFileArray = DataFilePeer::findDataFilesByPath($oTrial->getPathname(), 1, 25, $oTrial->getExperiment()->getProjectId(), $oTrial->getExperimentId(), $oTrial->getId(), 0, true);
              $_REQUEST["TrialFiles"] = serialize($oTrialDataFileArray);

              $this->setLayout("trial");
            }
            break;
        case "Repetition":
            $strEntityIds = JRequest::getVar('eid');
            if(!StringHelper::hasText($strEntityIds)){
              echo "<div><h2>Unable to Delete</h2></div>";
              echo ComponentHtml::showError("Please select at least 1 repetition.");
              return;
            }

            $strEntityIdArray = explode(",", $strEntityIds);
            if(count($strEntityIdArray) > 1){
              $oRepetitionArray = RepetitionPeer::retrieveByPKs($strEntityIdArray);
              $_REQUEST[RepetitionPeer::TABLE_NAME] = serialize($oRepetitionArray);

              $iDataFileCount = count($oRepetitionArray);

              //overwrite the single entity id with the list.
              $iEntityId = $strEntityIds;

              $this->setLayout("repetition_list");
            }else{
              /* @var $oRepetition Repetition */
              $oRepetition = $oModel->getRepetitionById($iEntityId);
              if(!$oRepetition){
                echo "<div><h2>Unable to Delete</h2></div>";
                echo ComponentHtml::showError("Repetition($iEntityId) not found.");
                return;
              }

              $strName = $oRepetition->getName();
              $strTitle = (StringHelper::hasText($oRepetition->getTitle())) ? $oRepetition->getTitle() : "Not available";
              $strDescription = (StringHelper::hasText($oRepetition->getDescription())) ? $oRepetition->getDescription() : "Not available";

              $oDataFileArray = DataFilePeer::findDataFiles($oHideExperimentArray, 1, 100, $oRepetition->getTrial()->getExperiment()->getProjectId(), $oRepetition->getTrial()->getExperimentId(), $oRepetition->getTrialId(), $oRepetition->getId(), true);
              $iDataFileCount = DataFilePeer::findDataFilesCount($oHideExperimentArray, $oRepetition->getTrial()->getExperiment()->getProjectId(), $oRepetition->getTrial()->getExperimentId(), $oRepetition->getTrialId(), $oRepetition->getId());

              $this->setLayout("repetition");
            }
            break;
        case "DataFile":
            $strEntityIds = JRequest::getVar('eid');
            if(!StringHelper::hasText($strEntityIds)){
              echo "<div><h2>Unable to Delete</h2></div>";
              echo ComponentHtml::showError("Please select at least 1 data file.");
              return;
            }

            $strEntityIdArray = explode(",", $strEntityIds);
            if(count($strEntityIdArray) > 1){
              $oDataFileArray = DataFilePeer::retrieveByPKs($strEntityIdArray);
              $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFileArray);

              $iDataFileCount = count($oDataFileArray);

              //overwrite the single entity id with the list.
              $iEntityId = $strEntityIds;

              $this->setLayout("data_file_list");
            }else{
              /* @var $oDataFile DataFile */
              $oDataFile = $oModel->getDataFileById($iEntityId);
              if(!$oDataFile){
                echo "<div><h2>Unable to Delete</h2></div>";
                echo ComponentHtml::showError("Data file ($iEntityId) not found.");
                return;
              }

              $strName = $oDataFile->getName();
              $strTitle = $oDataFile->getTitle();
              $strDescription = $oDataFile->getDescription();
              $strPath = $oDataFile->getFriendlyPath();
              $this->assignRef("filepath", $strPath);

              if($oDataFile->isDirectory()){
                $oDataFileLink = DataFileLinkPeer::retrieveByPK($oDataFile->getId());

                $oDataFileArray = DataFilePeer::findDataFilesByPath($oDataFile->getFullPath(), 1, 100, $oDataFileLink->getProjectId(), $oDataFileLink->getExperimentId(), $oDataFileLink->getTrialId(), $oDataFileLink->getRepId(), true);
                $iDataFileCount = DataFilePeer::findDataFilesByPathCount($oDataFile->getFullPath(), $oDataFileLink->getProjectId(), $oDataFileLink->getExperimentId(), $oDataFileLink->getTrialId(), $oDataFileLink->getRepId());
              }

              $this->setLayout("data_file");
            }
            break;
        case "Materials":
            /* @var $oMaterial Material */
            $oMaterial = $oModel->getMaterialById($iEntityId);
            if(!$oMaterial){
              echo "<div><h2>Unable to Delete</h2></div>";
              echo ComponentHtml::showError("Material($iEntityId) not found.");
              return;
            }

            $strName = $oMaterial->getName();
            $strTitle = $oMaterial->getMaterialType()->getSystemName();
            $strDescription = $oMaterial->getDescription();

            $strMaterialProperties = $oModel->findMaterialPropertiesByExperimentHTML($oMaterial);
            $strMaterialFiles = $oModel->findMaterialFilesByExperimentHTML($oMaterial);

            $this->setLayout("material");
            $this->assignRef("strMaterialProperties", $strMaterialProperties);
            $this->assignRef("strMaterialFiles", $strMaterialFiles);

            break;
        case "SensorLocationPlan":
            /* @var $oSensorLocationPlan SensorLocationPlan */
            $oSensorLocationPlan = $oModel->getLocationPlanById($iEntityId);
            if(!$oSensorLocationPlan){
              echo "<div><h2>Unable to Delete</h2></div>";
              echo ComponentHtml::showError("Sensor Location Plan($iEntityId) not found.");
              return;
            }

            $strName = $oSensorLocationPlan->getName();
            $strTitle = StringHelper::EMPTY_STRING;
            $strDescription = StringHelper::EMPTY_STRING;

            $oLocationArray = LocationPeer::findByLocationPlan($oSensorLocationPlan->getId());
            $_REQUEST[LocationPeer::TABLE_NAME] = serialize($oLocationArray);

            $this->setLayout("location_plan");

            break;
        case "SensorLocation":
            $strEntityIds = JRequest::getVar('eid');
            if(!StringHelper::hasText($strEntityIds)){
              echo "<div><h2>Unable to Delete</h2></div>";
              echo ComponentHtml::showError("Please select at least 1 sensor location.");
              return;
            }

            $strEntityIdArray = explode(",", $strEntityIds);
            if(count($strEntityIdArray) > 1){
              $oLocationArray = LocationPeer::retrieveByPKs($strEntityIdArray);
              $_REQUEST[LocationPeer::TABLE_NAME] = serialize($oLocationArray);

              //overwrite the single entity id with the list.
              $iEntityId = $strEntityIds;

              $this->setLayout("sensor_list");
            }else{
              /* @var $oLocation Location */
              $oLocation = $oModel->getLocationById($iEntityId);
              if(!$oLocation){
                echo "<div><h2>Unable to Delete</h2></div>";
                echo ComponentHtml::showError("Sensor Location($iEntityId) not found.");
                return;
              }

              $strName = $oLocation->getLabel();
              $strTitle = StringHelper::EMPTY_STRING;
              $strDescription = $oLocation->getComment();

              $_REQUEST[LocationPeer::TABLE_NAME] = serialize($oLocation);

              $this->setLayout("sensors");
            }

            break;
        default:
            $bEntityFound = false;
            break;
    }

    if($bEntityFound){
      $this->assignRef("entityId", $iEntityId);
      $this->assignRef("entityTypeId", $iEntityTypeId);
      $this->assignRef("name", $strName);
      $this->assignRef("title", $strTitle);
      $this->assignRef("description", $strDescription);
      $this->assignRef("className", $strClassName);
      $this->assignRef("path", $strPath);
      $this->assignRef("strReturnUrl", $strReturnUrl);
      $this->assignRef("iDataFileCount", $iDataFileCount);

      $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFileArray);
    }

    parent::display($tpl);
  }

}
?>
