<?php

/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright	Copyright 2010 by NEES
 */
// no direct access

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * 
 * 
 */
class sitesViewTrainingAndCertification extends JView {

    function display($tpl = null) {
        // Grab facility from Oracle
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);

        //echo(print_r($facility));

        $fac_name = $facility->getName();
        $fac_shortname = $facility->getShortName();
        $this->assignRef('FacilityName', $fac_name);
        $this->assignRef('facilityID', $facilityID);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document = &JFactory::getDocument();
        $pathway = & $mainframe->getPathway();
        $document->setTitle($fac_name);

        // Add facility name to breadcrumb
        $pathway->addItem($fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));

        // Add Sensor tab info to breadcrumb
        $pathway->addItem("Training and Certification", JRoute::_('index.php?option=com_sites&view=trainingandcertification&id=' . $facilityID));


        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(5, $facilityID);
        $this->assignRef('tabs', $tabs);

        //$fileBrowserObj = new DataFileBrowserSimple($facility);
        //$this->assignRef('fileBrowserObj', $fileBrowserObj);
        $infotype = "TrainingAndCertification";

        // Get all the files for the different file sections on this page

        $onSiteTrainingFiles = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "On Site Training", "");
        $onSiteTrainingDFs = array();
        foreach ($onSiteTrainingFiles as $fd) {
            /* @var $fd FacilityDataFile */
            $onSiteTrainingDFs[] = $fd->getDataFile();
        }

        
        $remoteTrainingFiles = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "Remote Training", "");
        $remoteTrainingDFs = array();
        foreach ($remoteTrainingFiles as $fd) {
            /* @var $fd FacilityDataFile */
            $remoteTrainingDFs[] = $fd->getDataFile();
        }


        $trainingFiles = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "Training", "");
        $trainingDFs = array();
        foreach ($trainingFiles as $fd) {
            /* @var $fd FacilityDataFile */
            $trainingDFs[] = $fd->getDataFile();
        }


        $safetyFiles = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "Training", "Safety Policy");
        $safetyDFs = array();
        foreach ($safetyFiles as $fd) {
            /* @var $fd FacilityDataFile */
            $safetyDFs[] = $fd->getDataFile();
        }


        $onSiteProceduresFiles = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "On Site Procedures", "");
        $onSiteProceduresDFs = array();
        foreach ($onSiteProceduresFiles as $fd) {
            /* @var $fd FacilityDataFile */
            $onSiteProceduresDFs[] = $fd->getDataFile();
        }


        $proposalPreparationFiles = FacilityDataFilePeer::findProposalPreparation($facilityID, $infotype, "Proposal Preparation");
        $proposalPreparationDFs = array();
        foreach ($proposalPreparationFiles as $fd) {
            /* @var $fd FacilityDataFile */
            $proposalPreparationDFs[] = $fd->getDataFile();
        }


        $additionalDocumentsFiles = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "Additional Documents", "");
        $additionalDocumentsDFs = array();
        foreach ($additionalDocumentsFiles as $fd) {
            /* @var $fd FacilityDataFile */
            $additionalDocumentsDFs[] = $fd->getDataFile();
        }


        // Lets pass these to the template
        $this->assignRef('onSiteTrainingDFs', $onSiteTrainingDFs);
        $this->assignRef('remoteTrainingDFs', $remoteTrainingDFs);
        $this->assignRef('trainingDFs', $trainingDFs);
        $this->assignRef('safetyDFs', $safetyDFs);
        $this->assignRef('onSiteProceduresDFs', $onSiteProceduresDFs);
        $this->assignRef('proposalPreparationDFs', $proposalPreparationDFs);
        $this->assignRef('additionalDocumentsDFs', $additionalDocumentsDFs);


        // Some rights based lookups
        $allowEdit = FacilityHelper::canEdit($facility);
        $this->assignRef('allowEdit', $allowEdit);
        $allowCreate = FacilityHelper::canCreate($facility);
        $this->assignRef('allowCreate', $allowCreate);

        // Code the redirect URL
        $uri  =& JURI::getInstance();
        $redirectURL = $uri->toString(array('path', 'query'));
        $redirectURL = base64_encode($redirectURL);
        $redirectURL = $redirectURL;
        $this->assignRef('redirectURL', $redirectURL);

        
        parent::display($tpl);
    }

    /**
     * Get a single DataFile for a facility by infoType, subInfoType and GroypBy
     *
     * @param String $info
     * @param String $sub
     * @param String $groupby
     * @return DataFile
     */
    function getFacilityDataFile($facilityid, $info, $sub, $groupby='') {
        $facDataFiles = FacilityDataFilePeer::findByDetails($facilityid, $info, $sub, $groupby);

        if (count($facDataFiles) > 0 && $ff = $facDataFiles[0]) {
            $df = $ff->getDataFile();

            if (!$df->getDeleted()) {
                return $df;
            }
        }

        return null;
    }

}
