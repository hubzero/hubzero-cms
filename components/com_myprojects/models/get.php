<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'api/org/nees/html/TabHtml.php';

class MyProjectsModelGet extends JModel{
	
  private $m_oMyProjectArray;
  private $m_oTreeTabArray;

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
    $this->m_oMyProjectArray = array("Projects");
    $this->m_oTreeTabArray = array("Projects");
  }

  /**
   *
   * @return Returns an array of tabs for the search results
   */
  public function getMyProjectsTabArray() {
    return $this->m_oMyProjectArray;
  }

  /**
   *
   */
  public function getTreeBrowserTabArray() {
    return $this->m_oTreeTabArray;
  }

  /**
   *
   * @return strTabs in html format
   */
  public function getTreeTab($p_strOption, $p_iId, $p_strTabArray, $p_strActive, $minimized) {
    return TabHtml::getTreeTab($p_strOption, $p_iId, $p_strTabArray, $p_strActive, $minimized);
  }

  /**
   *
   * @return strTabs in html format
   */
  public function getTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive) {
    return TabHtml::getTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive);
  }

  public function getCurrentUser() {
    $oUser =& JFactory::getUser();
    return $oUser;
  }

  public function getOracleUserByUsername($p_strUsername){
    if(empty($p_strUsername)){
      return null;
    }

    if($p_strUsername=="guest"){
      return null;
    }

    return PersonPeer::findByUserName($p_strUsername);
  }

  /**
   *
   * @param int $p_iPersonId
   * @return int
   */
  public function getMyProjectsCount($p_iPersonId){
    return ProjectPeer::getMyProjectsCount($p_iPersonId);
  }

  /**
   *
   * @param int $p_iPersonId
   * @param int $p_iLowerLimit
   * @param int $p_iUpperLimit
   * @return array
   */
  public function getMyProjectsWithPaging($p_iPersonId, $p_iLowerLimit, $p_iUpperLimit){
    return ProjectPeer::getMyProjectsWithPaging($p_iPersonId, $p_iLowerLimit, $p_iUpperLimit);
  }
  
  public function computeLowerLimit($p_iPageIndex, $p_iDisplay) {
    if ($p_iPageIndex == 0) {
        return 1;
    }
    return ($p_iDisplay * $p_iPageIndex) + 1;
  }

  public function computeUpperLimit($p_iPageIndex, $p_iDisplay) {
    if ($p_iPageIndex == 0) {
      return $p_iDisplay;
    }
    return $p_iDisplay * ($p_iPageIndex + 1);
  }
}

?>