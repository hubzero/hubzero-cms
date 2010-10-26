<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once('lib/data/OrganizationPeer.php');
require_once('lib/data/EquipmentPeer.php');

class ProjectEditorModelEquipment extends ProjectEditorModelBase{
	

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  public function findOrganizationByName($p_strSiteName){
  	return OrganizationPeer::findByName($p_strSiteName);
  }
  
  public function findAllMajorByOrganization($p_oOrganization){
  	return EquipmentPeer::findAllMajorByOrganization($p_oOrganization->getId());
  }
  
  public function findAllByParent($p_iParentId) {
  	return EquipmentPeer::findAllByParent($p_iParentId);
  }
  
  public function findAllByOrganization($p_iOrgid) {
  	return EquipmentPeer::findAllByOrganization($p_iOrgid) ;
  }
}

?>