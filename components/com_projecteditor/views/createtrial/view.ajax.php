<?php 

/**
 * @see components/com_projecteditor/models/uploadform.php
 * @see modules/mod_warehouseupload
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/data/TrialPeer.php';

class ProjectEditorViewCreateTrial extends JView{
	
  function display($tpl = null){
    //Incoming 
    $iProjectId = JRequest::getInt("projectId", 0);
    $iExperimentId = JRequest::getInt("experimentId", 0);
    $strReferer = $_SERVER['HTTP_REFERER'];

    /* @var $oModel ProjectEditorModelCreateTrial */
    $oModel =& $this->getModel();
    
    /* @var $oExperiment Experiment */
    $oExperiment = $oModel->getExperimentById($iExperimentId);
    if(!$oExperiment){
      echo "Experiment not selected.";
      return;
    }

    $oTrialArray = $oExperiment->getTrials();
    $_REQUEST[TrialPeer::TABLE_NAME] = serialize($oTrialArray);

    $this->assignRef("strPath", $oExperiment->getPathname());
    $this->assignRef("iProjectId", $iProjectId);
    $this->assignRef("iExperimentId", $iExperimentId);
    $this->assignRef("strReferer", $strReferer);
    
    parent::display($tpl);
  }
  
}

?>