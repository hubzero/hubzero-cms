<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');


class WarehouseModelFeatured extends JModel{

  private $m_oTabArray;
  private $m_oSearchTabArray;
  private $m_oSearchResultsTabArray;

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();

        $this->m_oTabArray = array("Project", "Experiments", "Data", "Team Members", "More");
        $this->m_oTabViewArray = array("project", "experiments", "data", "members", "more");

        $this->m_oSearchTabArray = array("Search", "Enhanced Projects");
        $this->m_oSearchTabViewArray = array("search","featured");

        $this->m_oSearchResultsTabArray = array("Results");
        $this->m_oSearchResultsTabViewArray = array("results");

        $this->m_oTreeTabArray = array("Projects");
        $this->m_oTreeTabViewArray = array("projects");
  }
  
  /**
   * 
   *
   */
  public function getFundingOrgs(){
  	return ProjectPeer::getFundingOrgs();
  }

  /**
     *
     * @return Returns an array of tabs for the selected warehouse
     */
    public function getTabArray() {
        return $this->m_oTabArray;
    }

    /**
     *
     * @return Returns an array of tab views for the selected warehouse
     */
    public function getTabViewArray() {
        return $this->m_oTabViewArray;
    }

    /**
     *
     * @return Returns an array of tabs for the search screen
     */
    public function getSearchTabArray() {
        return $this->m_oSearchTabArray;
    }

    /**
     *
     * @return Returns an array of tabs for the search screen
     */
    public function getSearchTabViewArray() {
        return $this->m_oSearchTabViewArray;
    }

    /**
     *
     */
    public function getTreeBrowserTabArray() {
        return $this->m_oTreeTabArray;
    }

    /**
     *
     */
    public function getTreeBrowserTabViewArray() {
        return $this->m_oTreeTabViewArray;
    }

    /**
     *
     * @return Returns an array of tabs for the search results
     */
    public function getSearchResultsTabArray() {
        return $this->m_oSearchResultsTabArray;
    }

    /**
     *
     * @return Returns an array of tabs for the search results
     */
    public function getSearchResultsTabViewArray() {
        return $this->m_oSearchResultsTabViewArray;
    }

    /**
     *
     * @return strTabs in html format
     */
    public function getTabs($p_strOption, $p_iId, $p_strTabArray, $p_strTabViewArray, $p_strActive) {
        return TabHtml::getTabs($p_strOption, $p_iId, $p_strTabArray, $p_strTabViewArray, $p_strActive);
    }

    /**
     *
     * @return strTabs in html format
     */
    public function getSubTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive) {
        return TabHtml::getSubTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive);
    }

    /**
     *
     * @return strTabs in html format
     */
    public function getTreeTab($p_strOption, $p_iId, $p_strTabArray, $p_strActive, $minimized) {
        return TabHtml::getTreeTab($p_strOption, $p_iId, $p_strTabArray, $p_strActive, $minimized);
    }
	
}

?>