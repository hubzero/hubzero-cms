<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
require_once 'libraries/joomla/application/module/helper.php';

class WarehouseViewPhotos extends JView {

    function display($tpl = null) {
        $iProjectId = JRequest::getVar('projectId');
        $this->assignRef('projectId', $iProjectId);

        $oProject = ProjectPeer::retrieveByPK($iProjectId);
        $_REQUEST[Search::SELECTED] = serialize($oProject);

        $iExperimentId = JRequest::getVar('experimentId');
        $this->assignRef('experimentId', $iExperimentId);

        $oExperiment = ExperimentPeer::retrieveByPK($iExperimentId);
        $_REQUEST[Experiments::SELECTED] = serialize($oExperiment);

        $iDisplay = JRequest::getVar('limit', 24);
        $iPageIndex = JRequest::getVar('index', 0);
        //echo "params=".$iPageIndex."/".$iDisplay."<br>";

        /* @var $oPhotosModel WarehouseModelPhotos */
        $oPhotosModel = & $this->getModel();
        $iLowerLimit = $oPhotosModel->computeLowerLimit($iPageIndex, $iDisplay);
        $iUpperLimit = $oPhotosModel->computeUpperLimit($iPageIndex, $iDisplay);
        //echo "limits=".$iLowerLimit."/".$iUpperLimit."<br>";

        $oPhotoDataFileArray = $oPhotosModel->findDataFileByMimeType($iProjectId, $iExperimentId, 0, 0, $iLowerLimit, $iUpperLimit);
        $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oPhotoDataFileArray);
        //echo "photo count=".sizeof($oPhotoDataFileArray)."<br>";

        $iResultsCount = $oPhotosModel->findDataFileByMimeTypeCount($iProjectId, $iExperimentId);
        //echo "results = ".$iResultsCount."<br>";
        $this->assignRef("photoCount", $iResultsCount);

        //get the tabs to display on the page
        $strTabArray = $oPhotosModel->getTabArray();
        $strTabViewArray = $oPhotosModel->getTabViewArray();
        $strTabHtml = $oPhotosModel->getTabs("warehouse", $iProjectId, $strTabArray, $strTabViewArray, "experiments");
        $this->assignRef("strTabs", $strTabHtml);

        // update and get the page views
        $iEntityViews = $oPhotosModel->getEntityPageViews(3, $oExperiment->getId());
        $this->assignRef("iEntityActivityLogViews", $iEntityViews);

        // update and get the page views
        $iEntityDownloads = $oPhotosModel->getEntityDownloads(3, $oExperiment->getId());
        $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

        $this->assignRef("mod_curationprogress", ComponentHtml::getModule("mod_curationprogress"));

        /*
         * grab the nees pagination object.  joomla's
         * pagination object doesn't handle the proper uri.
         */
        $oDbPagination = new DbPagination($iPageIndex, $iResultsCount, $iDisplay);
        $oDbPagination->computePageCount();
        $this->assignRef('pagination', $oDbPagination->getFooter24($_SERVER['REQUEST_URI'], "frmPhotos", "project-list"));

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

}
?>
