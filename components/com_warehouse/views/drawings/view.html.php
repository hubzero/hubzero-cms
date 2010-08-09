<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class WarehouseViewDrawings extends JView {

    function display($tpl = null) {
        $iProjectId = JRequest::getVar("projectId");
        $this->assignRef("projectId", $iProjectId);
        $oProject = ProjectPeer::retrieveByPK($iProjectId);
        $_REQUEST[Search::SELECTED] = serialize($oProject);

        $iExperimentId = JRequest::getVar("experimentId");
        $this->assignRef("experimentId", $iExperimentId);
        $oExperiment = ExperimentPeer::retrieveByPK($iExperimentId);
        $_REQUEST[Experiments::SELECTED] = serialize($oExperiment);

        /* @var $oDrawingsModel WarehouseModelDrawings */
        $oDrawingsModel = & $this->getModel();
        $oDrawingArray = $oDrawingsModel->findDataFileByEntityType("Drawing", $iProjectId, $iExperimentId);
        $oDrawingArray = $oDrawingsModel->resizePhotos($oDrawingArray);
        $_REQUEST["Drawings"] = serialize($oDrawingArray);

        $strTabArray = $oDrawingsModel->getTabArray();
        $strTabViewArray = $oDrawingsModel->getTabViewArray();
        $strTabHtml = $oDrawingsModel->getTabs( "warehouse", $iProjectId, $strTabArray, $strTabViewArray, "experiments" );
        $this->assignRef("strTabs", $strTabHtml);

        // update and get the page views
        $iEntityViews = $oDrawingsModel->getEntityPageViews(3, $oExperiment->getId());
        $this->assignRef("iEntityActivityLogViews", $iEntityViews);

        // update and get the page views
        $iEntityDownloads = $oDrawingsModel->getEntityDownloads(3, $oExperiment->getId());
        $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

        $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
        
        $bSearch = false;
        if (isset($_SESSION[Search::KEYWORDS])
            )$bSearch = true;
        if (isset($_SESSION[Search::SEARCH_TYPE])
            )$bSearch = true;
        if (isset($_SESSION[Search::FUNDING_TYPE])
            )$bSearch = true;
        if (isset($_SESSION[Search::MEMBER])
            )$bSearch = true;
        if (isset($_SESSION[Search::START_DATE])
            )$bSearch = true;
        if (isset($_SESSION[Search::END_DATE])
            )$bSearch = true;

        //set the breadcrumbs
        JFactory::getApplication()->getPathway()->addItem("Project Warehouse", "/warehouse");
        if ($bSearch) {
            JFactory::getApplication()->getPathway()->addItem("Results", "/warehouse/find?keywords=" . $_SESSION[Search::KEYWORDS]
                    . "&type=" . $_SESSION[Search::SEARCH_TYPE]
                    . "&funding=" . $_SESSION[Search::FUNDING_TYPE]
                    . "&member=" . $_SESSION[Search::MEMBER]
                    . "&startdate=" . $_SESSION[Search::START_DATE]
                    . "&startdate=" . $_SESSION[Search::END_DATE]);
        }
        JFactory::getApplication()->getPathway()->addItem($oProject->getName(), "/warehouse/project/$iProjectId");
        JFactory::getApplication()->getPathway()->addItem("Experiments", "/warehouse/experiments/$iProjectId");
        JFactory::getApplication()->getPathway()->addItem($oExperiment->getName(), "javascript:void(0)");

        parent::display($tpl);
    }

//end display
}
?>