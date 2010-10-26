<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once 'lib/data/MaterialType.php';
require_once 'lib/data/MaterialTypePeer.php';

class ProjectEditorViewMaterialTypes extends JView{
	
  function display($tpl = null){
    $iMaterialTypeId = JRequest::getInt('materialTypeId', 0);
    if($iMaterialTypeId==0){
      echo "Invalid material type";
      return;
    }

    /* @var $oModel ProjectEditorModelMaterialTypes */
    $oModel =& $this->getModel();

    /* @var $oMaterialType MaterialType */
    $oMaterialType = $oModel->find($iMaterialTypeId);
    $_REQUEST[MaterialTypePeer::TABLE_NAME] = serialize($oMaterialType);

    parent::display($tpl);
  }
  
}
?>
