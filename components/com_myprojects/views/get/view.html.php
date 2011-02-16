<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/Person.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'api/org/nees/html/TabHtml.php';
require_once 'api/org/nees/static/Search.php';
require_once 'api/org/nees/oracle/util/DbPagination.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';
require_once 'api/org/nees/util/StringHelper.php';

class MyProjectsViewGet extends JView{
	
  function display($tpl = null){
    /* @var $oModel MyProjectsModelGet */
    $oModel =& $this->getModel();

    $oResultsModel =& $this->getModel();
    $strTabArray = $oResultsModel->getMyProjectsTabArray();
    $strTabHtml = $oResultsModel->getTabs( "warehouse", 0, $strTabArray, "projects" );
    $this->assignRef( "strTabs", $strTabHtml );

    $strTreeTabArray = $oResultsModel->getTreeBrowserTabArray();
    $strTreeTabHtml = $oResultsModel->getTreeTab( "warehouse", 0, $strTreeTabArray, "projects" , false);
    $this->assignRef( "strTreeTabs", $strTreeTabHtml );

    // incoming
    $iDisplay = JRequest::getVar('limit', 25);
    $iIndex = JRequest::getVar('index', 0);

    // initialize the results
    $oProjectArray = array();
    $iProjectCount = 0;

    /* @var $oHubUser JUser */
    $oHubUser = $oModel->getCurrentUser();

    $iLowerLimit = 0;
    $iUpperLimit = 0;

    //if we have a hub user, look for projects
    if($oHubUser){
      /* @var $oPerson Person */
      $oPerson = $oModel->getOracleUserByUsername($oHubUser->username);
      //$oPerson = $oModel->getOracleUserByUsername("melorapark");

      //if the username is guest (person not logged in), they will not have projects
      if($oPerson){

        //find the upper and lower bounds for pagination
        $iLowerLimit = $oModel->computeLowerLimit($iIndex, $iDisplay);
        $iUpperLimit = $oModel->computeUpperLimit($iIndex, $iDisplay);

        $oProjectArray = $oModel->getMyProjectsWithPaging($oPerson->getId(), $iLowerLimit, $iUpperLimit);
        $iProjectCount = $oModel->getMyProjectsCount($oPerson->getId());
      }
    }
    $this->assignRef("oHubUser", $oHubUser->username);
    
    /*
     * store the results in the session.
     * get in the list in the tree module with unserialize($_SESSION[Search::RESULTS])
     */
    $_SESSION[Search::RESULTS] = serialize($oProjectArray);
    $_REQUEST[Search::COUNT] = $iProjectCount;

    //create thumbnails if need be...
    $strProjectIconArray = array();
    foreach($oProjectArray as $iProjectIndex=>$oProject){
      $strThumbnail =  $oProject->getProjectThumbnailHTML("icon");
      array_push($strProjectIconArray, $strThumbnail);
    }
    $_SESSION[Search::THUMBNAILS] = $strProjectIconArray;

    $oDbPagination = new DbPagination($iIndex, $iProjectCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmResults", "project-list"));

    $this->assignRef( "mod_treebrowser", ComponentHtml::getModule("mod_treebrowser") );
    
    parent::display($tpl);
  }
}
?>
