<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';
require_once 'lib/data/ExperimentDomainPeer.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/Organization.php';
require_once 'lib/data/Specimen.php';

class ProjectEditorViewExperiment extends JView{
	
  function display($tpl = null){
    /* @var $oExperimentModel ProjectEditorModelExperiment */
    $oExperimentModel =& $this->getModel();

    /*
     * facility is a parameter set if an error occurred.
     * If the parameter isn't set, clear session.
     */
    if(isset($_SESSION["ERRORS"])){
      //we got errors
    }else{
      if(!isset($_REQUEST['facility'])){
        $oExperimentModel->clearSession();
      }
    }
    
    $iProjectId = JRequest::getInt('projid', 0);
    $this->assignRef( "iProjectId", $iProjectId );

    $iExperimentId = JRequest::getInt('experimentId', 0);
    $this->assignRef( "iExperimentId", $iExperimentId );

    /* @var $oProject Project */
    $oProject = $oExperimentModel->getProjectById($iProjectId);
    $this->assignRef( "strProjectTitle", $oProject->getTitle() );
    
    //get the tabs to display on the page
    $strTabArray = $oExperimentModel->getTabArray();
    $strTabViewArray = $oExperimentModel->getTabViewArray();
    $strOption = "warehouse/projecteditor/project/$iProjectId";
    $strTabHtml = $oExperimentModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "experiments" );
    if(!$iExperimentId){
      /*
       * We're working with a new experiment.  Don't allow
       * users to click around until they save.
       */
      $strTabArray = $oExperimentModel->getCreateExperimentTabArray();
      $strTabViewArray = $oExperimentModel->getCreateExperimentTabViewArray();
      $strTabHtml = $oExperimentModel->getOnClickTabs( $strTabArray, $strTabViewArray, "experiments" );
    }
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'about');

    $strSubTabArray = $oExperimentModel->getExperimentsSubTabArray();
    $strSubTabViewArray = $oExperimentModel->getExperimentsSubTabViewArray();
    $strSubTabHtml = $oExperimentModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId/experiment", $iExperimentId, $strSubTabArray, $strSubTabViewArray, $strSubTab );
    if(!$iExperimentId){
      $strSubTabHtml = $oExperimentModel->getOnClickSubTabs( ProjectEditor::CREATE_EXPERIMENT_SUBTAB_ALERT, $strSubTabArray, $strSubTab );
    }
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    $this->assignRef( "oUser", $oExperimentModel->getCurrentUser() );

    $oExperimentDomainArray = $oExperimentModel->getExperimentDomains();
    $_REQUEST[ExperimentDomainPeer::TABLE_NAME] = serialize($oExperimentDomainArray);

    //set form fields
    $strBreadCrumbs = "Create Experiment";
    $strTitle = JRequest::getVar("title", StringHelper::EMPTY_STRING);
    $strStartDate = JRequest::getVar("startdate", date("m/d/Y"));
    $strEndDate = JRequest::getVar("enddate", "mm/dd/yyyy");
    $strFacility = StringHelper::EMPTY_STRING;
    $strFacilityPicked = StringHelper::EMPTY_STRING;
    $strDescription = JRequest::getVar("description", StringHelper::EMPTY_STRING);
    $iAccess = 4;
    $strSpecimenType = StringHelper::EMPTY_STRING;
    $strEquipmentList = "Enter one or more facilities (NEES Sites) above.";
    $strEquipmentPicked = StringHelper::EMPTY_STRING;
    $strTags = StringHelper::EMPTY_STRING;
    //$strExperimentImage = ProjectEditor::DEFAULT_PROJECT_IMAGE;
    $strExperimentImage = StringHelper::EMPTY_STRING;
    $strExperimentImageCaption = ProjectEditor::DEFAULT_EXPERIMENT_CAPTION;
    $bHasPhoto = false;
    $iExperimentDomainId = 0;
    $iEntityViews = 0;
    $iEntityDownloads = 0;
    $strCurationSubmitted = JRequest::getVar("submitted", "");

    if(isset($_REQUEST['facility'])){
      try{
        $oFacilityArray = $oExperimentModel->validateFacilitiesByName($_REQUEST['facility']);
        $strFacilityPicked = $this->getCurrentFacilitiesHTML($oFacilityArray);
      }catch(Exception $oException){
        //controller already captured the error already
      }
    }

    /* @var $oExperiment Experiment */
    $oExperiment = null;
    if($iExperimentId > 0){
      $oExperiment = $oExperimentModel->getExperimentById($iExperimentId);
      $_REQUEST[ExperimentPeer::TABLE_NAME] = serialize($oExperiment);

      $iExperimentDomainId = $oExperiment->getExperimentDomainId();

      $oAuthorizer = Authorizer::getInstance();
      if(!$oAuthorizer->canEdit($oExperiment)){
        echo ComponentHtml::showError(ProjectEditor::AUTHORIZER_EXPERIMENT_EDIT_ERROR);
        return;
      }

      $strBreadCrumbs = $oExperiment->getTitle();
      $strTitle = $oExperiment->getTitle();
      $strStartDate = $oExperiment->getStartDate();
      $strEndDate = $oExperiment->getEndDate();
      $strDescription = nl2br($oExperiment->getDescription());
      $iAccess = 4;
      $strSpecimenType = StringHelper::EMPTY_STRING;
      $strExperimentImage = $this->getExperimentImageLink($oExperiment,$strExperimentImageCaption);

      if(!StringHelper::hasText($strFacilityPicked)){
        $oFacilityArray = $oExperimentModel->findFacilityByExperiment($iExperimentId);
        $strFacilityPicked = $this->getCurrentFacilitiesHTML($oFacilityArray);       
      }

      $oExperimentEquipmentArray = $oExperimentModel->findEquipmentByExperimentId($iExperimentId);
      $strEquipmentPicked = $this->getCurrentEquipmentHTML($oExperimentEquipmentArray);

      /* @var $oSpecimen Specimen */
      $oSpecimen = $oExperimentModel->getSpecimenByProjectId($iProjectId);
      if($oSpecimen){
        $strSpecimenType = $oSpecimen->getName();
      }

      $oResearcherKeywordArray = $oExperimentModel->getResearcherKeywordsByEntity($iExperimentId, 3);
      if(empty ($oResearcherKeywordArray)){
        $oOntologyArray = $oExperimentModel->getOntologyByProjectId($iExperimentId);
        $strTags = $oExperimentModel->getOntologyInputHTML($oOntologyArray);
      }else{
        $strTags = $oExperimentModel->getResearcherKeywordsInputHTML($oResearcherKeywordArray);
      }

      $iEntityViews = $oExperimentModel->getEntityPageViews(3, $oExperiment->getId());
      $iEntityDownloads = $oExperimentModel->getEntityDownloads(3, $oExperiment->getId());
    }

    $oEntityType = EntityTypePeer::findByTableName(ProjectEditor::EXPERIMENT_IMAGE);
    $this->assignRef( "iUsageTypeId", $oEntityType->getId() );

    $this->assignRef( "iExperimentDomainId", $iExperimentDomainId );
    $this->assignRef( "strTitle", $strTitle );
    $this->assignRef( "strStartDate", $strStartDate );
    $this->assignRef( "strEndDate", $strEndDate );
    $this->assignRef( "strFacility", $strFacility );
    $this->assignRef( "strFacilityPicked", $strFacilityPicked );
    $this->assignRef( "strDescription", $strDescription );
    $this->assignRef( "iAccess", $iAccess );
    $this->assignRef( "strEquipmentList", $strEquipmentList );
    $this->assignRef( "strEquipmentPicked", $strEquipmentPicked );
    $this->assignRef( "oExperimentDomainArray", $oExperimentDomainArray );
    $this->assignRef( "strSpecimenType", $strSpecimenType );
    $this->assignRef( "strTags", $strTags );
    $this->assignRef( "strExperimentImage", $strExperimentImage );
    $this->assignRef( "strExperimentImageCaption", $strExperimentImageCaption );
    $this->assignRef( "bHasPhoto", $bHasPhoto );

    $this->assignRef("iEntityActivityLogViews", $iEntityViews);
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

    $this->assignRef( "submitted", $strCurationSubmitted);

    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"/warehouse/projecteditor/project/".$oProject->getId());
    JFactory::getApplication()->getPathway()->addItem("Experiments","/warehouse/projecteditor/project/".$oProject->getId()."/experiments");
    JFactory::getApplication()->getPathway()->addItem($strBreadCrumbs,"/warehouse/projecteditor/project/".$oProject->getId()."/experiment/".$iExperimentId);
    JFactory::getApplication()->getPathway()->addItem("About","javascript:void(0)");

    if($oExperiment){
      $_REQUEST[Experiments::SELECTED] = serialize($oExperiment);
      $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
    }else{
      $strBlank = StringHelper::EMPTY_STRING;
      $this->assignRef( "mod_curationprogress", $strBlank );
    }

    parent::display($tpl);
  }

  private function getCurrentFacilitiesHTML($p_oOrganizationArray){
    $strReturn = StringHelper::EMPTY_STRING;

    /* @var $oOrganization Organization */
    foreach ($p_oOrganizationArray as $iIndex=>$oOrganization){
      $strInput = $oOrganization->getName();
      $strInputDiv = "facility-".$iIndex."Input";
      $strFieldArray = "facility[]";
      $strFieldPicked = "facilityPicked";
      $strRemoveDiv = "facility-".$iIndex."Remove";

      $strReturn .= <<< ENDHTML

          <div id="$strInputDiv" class="editorInputFloat editorInputSize">
            <input type="hidden" name="$strFieldArray" value="$strInput"/>
            $strInput
          </div>
          <div id="$strRemoveDiv" class="editorInputFloat editorInputButton">
            <a href="javascript:void(0);" title="Remove $strInput." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/remove?format=ajax', 'facility', $iIndex, '$strFieldPicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
          </div>
          <div class="clear"></div>

ENDHTML;

    }
    
    return $strReturn;
  }

  private function getCurrentEquipmentHTML($p_oExperimentEquipmentArray){
    $strReturn = StringHelper::EMPTY_STRING;

    /* @var $oExperimentEquipment ExperimentEquipment */
    foreach ($p_oExperimentEquipmentArray as $iIndex=>$oExperimentEquipment){
      $iEquipmentId = $oExperimentEquipment->getEquipment()->getId();
      $iModelId = $oExperimentEquipment->getEquipment()->getModelId();
      $iOrganizationId = $oExperimentEquipment->getEquipment()->getOrganizationId();
      $strInput = $oExperimentEquipment->getEquipment()->getName();
      $strInputDiv = "equipment-".$iIndex."Input";
      $strFieldArray = "equipment[]";
      $strFieldPicked = "equipmentPicked";
      $strRemoveDiv = "equipment-".$iIndex."Remove";

      //silly me!  just use the equipment id
      //<input type="hidden" name="$strFieldArray" value="$strInput:::$iModelId:::$iOrganizationId" style="width:100%"/>
      $strReturn .= <<< ENDHTML

          <div id="$strInputDiv" class="editorInputFloat editorInputSize">
            <input type="hidden" name="$strFieldArray" value="$iEquipmentId" style="width:100%"/>
            <span style="width:100%">$strInput</span>
          </div>
          <div id="$strRemoveDiv" class="editorInputFloat editorInputButton">
            <a href="javascript:void(0);" title="Remove $strInput." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/remove?format=ajax', 'facility', $iIndex, '$strFieldPicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
          </div>
          <div class="clear"></div>

ENDHTML;

    }

    return $strReturn;
  }

  /**
   *
   * @param Experiment $p_oExperiment
   * @return string
   */
  private function getExperimentImageLink($p_oExperiment, &$p_strCaption){
    $default_thumbnail = "";

    $expImage = $p_oExperiment->getExperimentThumbnailDataFile();

    $thumbnail = null;
    if($expImage && file_exists($expImage->getFullPath())) {
      //creates the thumbnail if it doesn't exist
      $expThumbnailId = $expImage->getImageThumbnailId();

      if($expThumbnailId && $expThumbnail = DataFilePeer::find($expThumbnailId)) {
        if(file_exists($expThumbnail->getFullPath())) {
          $strDisplayName = "display_".$expImage->getId()."_".$expImage->getName();
          $expImage->setName($strDisplayName);
          $expImage->setPath($expThumbnail->getPath());
          $thumbnail = "<a title='".$expImage->getDescription()."' style='border-bottom:0px;' target='_blank' href='" . $expImage->getUrl() . "' rel='lightbox[experiments]'>View Photo</a>";
          $p_strCaption = (StringHelper::hasText($expImage->getDescription())) ? $expImage->getDescription() : ProjectEditor::DEFAULT_EXPERIMENT_CAPTION;
        }
      }
    }

    if(!$thumbnail) $thumbnail = $default_thumbnail;

    return $thumbnail;
  }
  
}
?>
