<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class CurateViewExperiment extends JView{
	
  function display($tpl = null){
    $oModel =& $this->getModel();
    
    $nExperimentId = JRequest::getVar('expid');
    $nProjectId = JRequest::getVar('projectId');    
    
    $this->assignRef('experimentId', $nExperimentId);
    $this->assignRef('projectId', $nProjectId);
    
    $oExperimentArray = $oModel->getExperiments($nProjectId);
    $this->assignRef('experimentDropDownArray', $oExperimentArray);
    
    #find the given experiment
    $oExperiment = $oModel->getCuratedExperimentById($nExperimentId);
    $this->assignRef('experimentArray', $oExperiment);
    
    #find all of the curation object types
    $oCurationObjectTypeArray = $oModel->getCurationObjectTypes();
    $this->assignRef( 'curatedObjectTypeArray', $oCurationObjectTypeArray );
    
    $oProject = $oModel->getProjectById($nProjectId);
    
    #find all of the experiment documents
    $oExperimentDocumentArray = $oModel->getExperimentDocumentsAll($oProject["NAME"], $oExperiment["EXPERIMENT_NAME"], 0, $oCurationObjectTypeArray, $oExperiment['EXPERIMENT_CURATION_STATUS']);
    $this->assignRef( 'experimentDocumentArray', $oExperimentDocumentArray );
    
    $nCuratedId = $oExperiment['CURATED_ID'];
    
    /*
     * initialize the links for the ajax calls.  we will either edit or save data.
     */
    $sInputFieldPrefix = "/curate?task=showform&format=ajax&type=1&curated=".$nCuratedId;
    $sTextAreaPrefix = "/curate?task=showform&format=ajax&type=2&curated=".$nCuratedId;
    $sSaveFieldPrefix = "/curate?task=saveform&format=ajax"; 
    
    #set the ajax call for name
    $sName = $oExperiment['EXPERIMENT_NAME'];
    if(!empty($oProject['CURATED_NAME']))$sName = $oProject['CURATED_NAME'];
    //$sNameAjax = $oModel->getAjaxHandler("Name", $sName, $sInputFieldPrefix."&name=txtProjectName&value=".$sName."&label=Name&return=projectName&method=name&column=name", $nCuratedId/*$sSavePrefix*/, "projectName", "txtProjectName");
    $sNameAjax = $oModel->getHiddenInput("Name", $sName, $sInputFieldPrefix."&name=txtProjectName&value=".$sName."&label=Name&return=projectName&method=name&column=name", $nCuratedId/*$sSavePrefix*/, "projectName", "txtProjectName");
    $this->assignRef('curationNameAjax', $sNameAjax);
    
    #set the ajax call for object type
    $sObjectType = "Experiment";
    if(!empty($oExperiment['CURATED_OBJECT_TYPE']))$sObjectType = $oExperiment['CURATED_OBJECT_TYPE'];
    //$sObjectTypeAjax = $oModel->getAjaxHandler("Object Type", $sObjectType, $sInputFieldPrefix."&name=txtProjectObjectType&value=".$sObjectType."&label=Object Type&return=projectObjectType&method=objecttype&column=object_type", $nCuratedId/*$sSaveFieldPrefix*/, "projectObjectType", "txtProjectObjectType");
    $sObjectTypeAjax = $oModel->getHiddenInput("Object Type", $sObjectType, $sInputFieldPrefix."&name=txtProjectObjectType&value=".$sObjectType."&label=Object Type&return=projectObjectType&method=objecttype&column=object_type", $nCuratedId/*$sSaveFieldPrefix*/, "projectObjectType", "txtProjectObjectType");
    $this->assignRef('curationObjectTypeAjax', $sObjectTypeAjax);
    
    #set the ajax call for curation state
    $sCurationState = $oExperiment['EXPERIMENT_CURATION_STATUS'];
    if(!empty($oExperiment['CURATED_OBJECT_STATUS']))$sCurationState = $oExperiment['CURATED_OBJECT_STATUS'];
    $sCurationStateAjax = $oModel->getAjaxHandler("Curation State", $sCurationState, $sInputFieldPrefix."&name=txtProjectCurationState&value=".$sCurationState."&label=Curation State&return=projectCurationState&method=curatestate&column=curation_state", $nCuratedId/*$sSaveFieldPrefix*/, "projectCurationState", "txtProjectCurationState");
    $this->assignRef('curationStateAjax', $sCurationStateAjax);
    
    #set the ajax call for short title
    $sShortTitle = $oExperiment['EXPERIMENT_SHORT_TITLE'];
    if(!empty($oExperiment['CURATED_SHORT_TITLE']))$sShortTitle = $oExperiment['CURATED_SHORT_TITLE'];
    $sShortTitleAjax = $oModel->getAjaxHandler("Short Title", $sShortTitle, $sInputFieldPrefix."&name=txtProjectShortTitle&value=".$sShortTitle."&label=Short Title&return=projectShortTitle&method=shorttitle&column=short_title", $nCuratedId/*$sSaveFieldPrefix*/, "projectShortTitle", "txtProjectShortTitle");
    $this->assignRef('shortTitleAjax', $sShortTitleAjax);
    
    #set the ajax call for version
    $sVersion = $oExperiment['VERSION'];
    if(empty($sVersion))$sVersion="0";
    if(!empty($oExperiment['CURATED_VERSION']))$sVersion = $oExperiment['CURATED_VERSION'];
    $sVersionAjax = $oModel->getAjaxHandler("Version", $sVersion, $sInputFieldPrefix."&name=txtProjectVersion&value=".$sVersion."&label=Version&return=projectVersion&method=version&column=version", $nCuratedId/*$sSaveFieldPrefix*/, "projectVersion", "txtProjectVersion");
    $this->assignRef('versionAjax', $sVersionAjax);
    
    #set the ajax call for curated
    $sCuratedDate = date("m-d-Y");
    if(!empty($oExperiment['CURATE_OBJECT_CREATED_DATE']))$sCuratedDate = $oExperiment['CURATE_OBJECT_CREATED_DATE'];
    $sCuratedDateAjax = $oModel->getAjaxHandler("Curate Date", $sCuratedDate, $sInputFieldPrefix."&name=txtProjectCurated&value=".$sCuratedDate."&label=Curate Date&return=projectCurated&method=curatedate&column=curated_date", $nCuratedId/*$sSaveFieldPrefix*/, "projectCurated", "txtProjectCurated");
    $this->assignRef('curatedDateAjax', $sCuratedDateAjax);
    
    #set the ajax call for curated
    //$sStartDateAjax = $oModel->getAjaxHandler("Start Date", $oExperiment['EXPERIMENT_START_DATE'], $sInputFieldPrefix."&name=txtProjectStartDate&value=".$oExperiment['EXPERIMENT_START_DATE']."&label=StartDate&return=projectStartDate&method=startdate", $nCuratedId/*$sSaveFieldPrefix*/, "projectStartDate", "txtProjectStartDate");
    $sStartDateAjax = $oModel->getHiddenInput("Start Date", $oExperiment['EXPERIMENT_START_DATE'], $sInputFieldPrefix."&name=txtProjectStartDate&value=".$oExperiment['EXPERIMENT_START_DATE']."&label=StartDate&return=projectStartDate&method=startdate", $nCuratedId/*$sSaveFieldPrefix*/, "projectStartDate", "txtProjectStartDate");
    //$sStartDateAjax = "Start Date: ".$oExperiment['EXPERIMENT_START_DATE'];
    $this->assignRef('startDateAjax', $sStartDateAjax);
    
    #set the ajax call for contact
    //$sContactNameAjax = $oModel->getAjaxHandler("Contact", $oExperiment['CONTACT_NAME'], $sInputFieldPrefix."&name=txtProjectContactName&value=".$oExperiment['CONTACT_NAME']."&label=ContactName&return=projectContactName&method=contact", $nCuratedId/*$sSaveFieldPrefix*/, "projectContactName", "txtProjectContactName");
    $sContactNameAjax = $oModel->getHiddenInput("Contact", $oExperiment['CONTACT_NAME'], $sInputFieldPrefix."&name=txtProjectContactName&value=".$oExperiment['CONTACT_NAME']."&label=ContactName&return=projectContactName&method=contact", $nCuratedId/*$sSaveFieldPrefix*/, "projectContactName", "txtProjectContactName");
    $this->assignRef('contactNameAjax', $sContactNameAjax);
    
    #set the ajax call for visibility
    $sVisibility = $oExperiment['EXPERIMENT_VIEWABLE'];
    if(!empty($oExperiment['CURATED_OBJECT_VISIBILITY']))$sVisibility = $oExperiment['CURATED_OBJECT_VISIBILITY'];
    //$sVisibilityAjax = $oModel->getAjaxHandler("Visibility", $sVisibility, $sInputFieldPrefix."&name=txtProjectVisibility&value=".$sVisibility."&label=Visibility&return=projectVisibility&method=visibility&column=object_visibility", $nCuratedId/*$sSaveFieldPrefix*/, "projectVisibility", "txtProjectVisibility");
    $sVisibilityAjax = $oModel->getHiddenInput("Visibility", $sVisibility, $sInputFieldPrefix."&name=txtProjectVisibility&value=".$sVisibility."&label=Visibility&return=projectVisibility&method=visibility&column=object_visibility", $nCuratedId/*$sSaveFieldPrefix*/, "projectVisibility", "txtProjectVisibility");
    $this->assignRef('visibilityAjax', $sVisibilityAjax);
    
    #set the ajax call for status
    $sStatus = strtoupper($oExperiment['EXPERIMENT_STATUS']);
    if(!empty($oExperiment['CURATED_OBJECT_STATUS']))$sStatus = $oExperiment['CURATED_OBJECT_STATUS'];
    //$sStatusAjax = $oModel->getAjaxHandler("Status", $sStatus, $sInputFieldPrefix."&name=txtProjectStatus&value=".$sStatus."&label=Status&return=projectStatus&return=projectStatus&method=status&column=object_status", $nCuratedId/*$sSaveFieldPrefix*/, "projectStatus", "txtProjectStatus");
    $sStatusAjax = $oModel->getHiddenInput("Status", $sStatus, $sInputFieldPrefix."&name=txtProjectStatus&value=".$sStatus."&label=Status&return=projectStatus&return=projectStatus&method=status&column=object_status", $nCuratedId/*$sSaveFieldPrefix*/, "projectStatus", "txtProjectStatus");
    $this->assignRef('statusAjax', $sStatusAjax);
    
    #set the ajax call for link
    $sLink = $oExperiment['LINK'];
    if(empty($sLink))$sLink = '/Experiment/'.$oExperiment['PROJID'];
    if(!empty($oExperiment['CURATED_LINK']))$sLink = $oExperiment['CURATED_LINK'];
    //$sLinkAjax = $oModel->getAjaxHandler("Link", $sLink, $sInputFieldPrefix."&name=txtProjectLink&value=".$sLink."&label=Link&return=projectLink&method=link&column=link", $nCuratedId/*$sSaveFieldPrefix*/, "projectLink", "txtProjectLink");
    $sLinkAjax = $oModel->getHiddenInput("Link", $sLink, $sInputFieldPrefix."&name=txtProjectLink&value=".$sLink."&label=Link&return=projectLink&method=link&column=link", $nCuratedId/*$sSaveFieldPrefix*/, "projectLink", "txtProjectLink");
    $this->assignRef('linkAjax', $sLinkAjax);
    
    #set the ajax call for it contact
    $strITContact = "";//$oExperiment['CURATED_IT_CONTACT'];
    //$sITContactAjax = $oModel->getAjaxHandler("IT Contact", $strITContact, $sInputFieldPrefix."&name=txtProjectITContact&value=".$strITContact."&label=IT Contact&return=projectITContact&method=itcontact&column=it_contact", $nCuratedId/*$sSaveFieldPrefix*/, "projectITContact", "txtProjectITContact");
    $sITContactAjax = $oModel->getHiddenInput("IT Contact", $strITContact, $sInputFieldPrefix."&name=txtProjectITContact&value=".$strITContact."&label=IT Contact&return=projectITContact&method=itcontact&column=it_contact", $nCuratedId/*$sSaveFieldPrefix*/, "projectITContact", "txtProjectITContact");
    $this->assignRef('itContactAjax', $sITContactAjax);
    
    #set the ajax call for description
    $sDescription = $oExperiment['PROJECT_DESCRIPTION'];
    if(!empty($oExperiment['CURATED_DESCRIPTION']))$sDescription = $oExperiment['CURATED_DESCRIPTION'];
    $sDescriptionAjax = $oModel->getAjaxTextAreaHandler("Description", $sDescription, $sTextAreaPrefix."&name=txtProjectDescription&value=".$sDescription."&label=Description&return=projectDescription&method=description&column=description", $nCuratedId/*$sSaveFieldPrefix*/, "projectDescription", "txtProjectDescription");
    $this->assignRef('descriptionAjax', $sDescriptionAjax);
    
    parent::display($tpl);
  }
}
?>
