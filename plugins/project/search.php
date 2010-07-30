<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import library dependencies
jimport('joomla.event.plugin');
 
class plgProjectSearch extends JPlugin{
	
   /**
    * Constructor
    *
    * 
    */
  function plgProjectSearch( &$subject ){
    parent::__construct( $subject );
 
    // load plugin parameters
    $this->_plugin = JPluginHelper::getPlugin( 'project', 'search' );
    $this->_params = new JParameter( $this->_plugin->params );
  }
  
  /**
    * Plugin method with the same name as the event will be called automatically.
    */
  function onProjectSearchForm(&$params){
    global $mainframe;
    
    $strQuery = $this->getSearchFormQuery();
    $strResultsArray = ProjectPeer::searchByForm($strQuery);
    
    /*
     * store the search for historical purposes.
     */
    if(!empty($strResultsArray)){
      $oUser =& JFactory::getUser();
    	
      $strUsername = ($oUser->guest==0) ? $oUser->username : "guest";
      $strKeyword =	JRequest::getVar("keywords", "");
      if(strlen($strKeyword)>0){
        $strDate = date("Y-m-d");
      
	    $oSearchLog = $this->saveSearch($strUsername, $strKeyword, $strQuery, $strDate);
      }
    }
    
    return $strResultsArray; 
  }
  
  /**
   * Build the query for processing the search form. 
   * 
   * @see /www/neeshub/components/com_warehouse/views/search/tmpl/default.php
   */
  private function getSearchFormQuery(){
  	$strKeywords = JRequest::getVar('keywords');
  	$strType = JRequest::getVar('type');
  	$strFunding = JRequest::getVar('funding');
  	$strMember = JRequest::getVar('member');
  	$strStartDate = JRequest::getVar('startdate');
  	$strEndDate = JRequest::getVar('enddate');
  	$strOrderBy = JRequest::getVar('order', 'nickname');
  	$iLimitStart = JRequest::getVar('limitstart', 0);
    $iDisplay = JRequest::getVar('limit', 25);
    $iPageIndex = JRequest::getVar('index', 0);
  	
  	$strCondition = "";
  	
  	//set the keywords condition
  	$strKeywordsCondition = $this->getKeywordsCondition($strKeywords);
  	if(strlen($strKeywordsCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strKeywordsCondition;
  	}
  	
  	//set the funding condition
  	$strFundingCondition = $this->getFundingCondition($strFunding);
    if(strlen($strFundingCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strFundingCondition;
  	}
  	
  	//set the start date condition
  	$strStartDateCondition = $this->getStartDateCondition($strStartDate);
    if(strlen($strStartDateCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strStartDateCondition;
  	}
  	
  	//set the end date condition
    $strEndDateCondition = $this->getEndDateCondition($strEndDate);
    if(strlen($strEndDateCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strEndDateCondition;
  	}
  	
  	//set the member condition
    $strMemberCondition = $this->getMemberCondition($strMember);
    if(strlen($strMemberCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strMemberCondition;
  	}
  	
  	//find the upper and lower bounds for pagination (joomla)
//  $iLowerLimit = $this->computeLowerLimit($iLimitStart);
//  $iUpperLimit = $this->computeUpperLimit($iLowerLimit, $iDisplay);
    
    $iLowerLimit = $this->computeLowerLimit($iPageIndex, $iDisplay);
    $iUpperLimit = $this->computeUpperLimit($iPageIndex, $iDisplay);
    
    //echo $iPageIndex.", ".$iLowerLimit.", ".$iUpperLimit."<br>";
    
    $strQuery = "SELECT * 
				 FROM (
				   SELECT PROJECT.PROJID, row_number() 
				   OVER (ORDER BY PROJECT.$strOrderBy desc) as rn 
				   FROM PROJECT ";
  	
  	//if we have a name, join the following tables...
  	if(strlen($strMemberCondition)>0){
  	  $strQuery = $strQuery . ", 
  	  					PERSON,
          				PERSON_ENTITY_ROLE, 
          				ROLE 
  	  			";
  	}
  	$strQuery = $strQuery . 
				"  WHERE PROJECT.deleted = 0 
					 AND upper(PROJECT.viewable)='PUBLIC'  
				     $strCondition 
				 )  
				 WHERE rn BETWEEN $iLowerLimit AND $iUpperLimit";

	//echo $strQuery."<br>";
			   
	return $strQuery;			     
  }
  
  /**
   * Cont the number of records for the project query.
   * @return string
   */
  private function getSearchFormQueryCount(){
  	$strKeywords = JRequest::getVar('keywords');
  	$strType = JRequest::getVar('type');
  	$strFunding = JRequest::getVar('funding');
  	$strMember = JRequest::getVar('member');
  	$strStartDate = JRequest::getVar('startdate');
  	$strEndDate = JRequest::getVar('enddate');
  	$strOrderBy = JRequest::getVar('order', 'name');
  	$iLimitStart = JRequest::getVar('limitstart', 0);
    $iDisplay = JRequest::getVar('limit', 25);
  	
  	$strCondition = "";
  	$strKeywordsCondition = $this->getKeywordsCondition($strKeywords);
  	if(strlen($strKeywordsCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strKeywordsCondition;
  	}
  	
  	$strFundingCondition = $this->getFundingCondition($strFunding);
    if(strlen($strFundingCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strFundingCondition;
  	}
  	
  	$strStartDateCondition = $this->getStartDateCondition($strStartDate);
    if(strlen($strStartDateCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strStartDateCondition;
  	}
  	
    $strEndDateCondition = $this->getEndDateCondition($strEndDate);
    if(strlen($strEndDateCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strEndDateCondition;
  	}
  	
    $strMemberCondition = $this->getMemberCondition($strMember);
    if(strlen($strMemberCondition)>0){
  	  $strCondition = $strCondition . " AND ". $strMemberCondition;
  	}
  	
  	$iLowerLimit = $this->computeLowerLimit($iLimitStart);
    $iUpperLimit = $this->computeUpperLimit($iLowerLimit, $iDisplay);
  	
  	$strQuery = "SELECT COUNT(PROJECT.PROJID) AS TOTAL 
				 FROM PROJECT ";
  	if(strlen($strMemberCondition)>0){
  	  $strQuery = $strQuery . ", 
  	  					PERSON,
          				PERSON_ENTITY_ROLE, 
          				ROLE 
  	  			";
  	}
  	$strQuery = $strQuery . 
				"  WHERE PROJECT.deleted = 0 
					 AND upper(PROJECT.viewable)='PUBLIC'  
				     $strCondition";
				     
	//echo "count - ".$strQuery."<br>";			    
	return $strQuery;			     
  }
  
  /**
   * Get the keyword conditions
   * @return string
   */
  private function getKeywordsCondition($p_strSearchTerms){
  	if(!$p_strSearchTerms || $p_strSearchTerms == ""){
  	  return "";
  	}
  	
  	$p_strSearchTerms = "%$p_strSearchTerms%";
  	$strSearchColumns = ProjectPeer::getKeywordSearchColumns();
  	
  	//see if any of the searchable columns contain a search term
    $strConditionArray = array();
    foreach($strSearchColumns as $strColumn){
      $strConditionArray[] = "lower(" . $strColumn . ") LIKE '" . strtolower($p_strSearchTerms) . "'";
    }
    
    //turn the array into a single string
    $strCondition = implode($strConditionArray, " OR ");
    $strCondition = "(" . $strCondition . ")";
    
    return $strCondition;
  }
  
  /**
   * Get the funding condition.
   * @return string
   */
  private function getFundingCondition($p_strFunding){
  	if(!$p_strFunding || $p_strFunding == ""){
  	  return "";
  	}
  	
  	$p_strFunding = "%$p_strFunding%";
  	return "lower(". ProjectPeer::FUNDORG .") LIKE '" . strtolower($p_strFunding) ."'";
  }
  
  /**
   * Get the start date condition
   * @return string
   */
  private function getStartDateCondition($p_strStartDate){
    if(!$p_strStartDate || $p_strStartDate == "" || $p_strStartDate == "mm/dd/yyyy"){
  	  return "";
  	}
  	
  	return ProjectPeer::START_DATE . " >= to_date('$p_strStartDate', 'MM/dd/yyyy')";
  }
  
  /**
   * Get the end date condition
   * @return string
   */
  private function getEndDateCondition($p_strEndDate){
    if(!$p_strEndDate || $p_strEndDate == "" || $p_strEndDate == "mm/dd/yyyy"){
  	  return "";
  	}
  	
  	return ProjectPeer::END_DATE . " <= to_date('$p_strEndDate', 'MM/dd/yyyy')";
  }
  
  /**
   * Get the associative member condition.  
   * Joins in person, person_entity_role, and role tables. 
   * @return string
   */
  private function getMemberCondition($p_strMember){
    if(!$p_strMember || $p_strMember == "" || $p_strMember =="Last Name, First Name" ){
  	  return "";
  	}
  	
  	$strMemberCondition = "";
  	$strMemberArray = explode(",", $p_strMember);
  	if(sizeof($strMemberArray)==2){
  	  $strMemberCondition = $strMemberCondition . "UPPER(".PersonPeer::LAST_NAME.") like '%". strtoupper(trim($strMemberArray[0])) ."%' AND " .
  	  											  "UPPER(".PersonPeer::FIRST_NAME.") like '%". strtoupper(trim($strMemberArray[1])) ."%' AND ";
  	}elseif(sizeof($strMemberArray)==1){
  	  $strMemberCondition = $strMemberCondition . "UPPER(".PersonPeer::LAST_NAME.") like '%". strtoupper(trim($strMemberArray[0])) ."%' AND ";
  	}
  	
  	$strMemberCondition = $strMemberCondition .  
  						  " PERSON.ID = PERSON_ENTITY_ROLE.PERSON_ID AND 
					        PERSON_ENTITY_ROLE.ROLE_ID = ROLE.ID AND 
					        PERSON_ENTITY_ROLE.ENTITY_ID = PROJECT.PROJID AND
					        PERSON_ENTITY_ROLE.ENTITY_TYPE_ID = 1 ";
  	
  	return $strMemberCondition;
  }
  
  /**
    * Plugin method with the same name as the event will be called automatically.
    */
  function onProjectSearchFormCount(&$params){
    global $mainframe;
 
    return ProjectPeer::searchByFormCount($this->getSearchFormQueryCount(), "TOTAL");
  }
  
  /**
    * Plugin method with the same name as the event will be called automatically.
    */
  function onProjectSearchTag($searchquery, $limit=0, $limitstart=0, $areas=null){
    global $mainframe;
 
    // Plugin code goes here.
      
 	$temp = array();
 	$temp['search']="tag";
 	  
    return $temp;
  }
  
  /**
    * Plugin method with the same name as the event will be called automatically.
    */
  function onProjectSearchPopular($searchquery, $limit=0, $limitstart=0, $areas=null){
    global $mainframe;
 
    // Plugin code goes here.
    
 	$temp = array();
 	$temp['search']="popular";
 	  
    return $temp;
  }
  
//  private function computeLowerLimit($p_iLowerLimit){
//  	if($p_iLowerLimit==0){
//  	  return 1;	
//  	}
//  	return $p_iLowerLimit;
//  }
  
  private function computeLowerLimit($p_iPageIndex, $p_iDisplay){
  	if($p_iPageIndex==0){
  	  return 1;	
  	}
  	return ($p_iPageIndex * $p_iDisplay) + 1;
  }
  
//  private function computeUpperLimit($p_iLowerLimit, $p_iDisplay){
//  	if($p_iLowerLimit==1){
//  	  return $p_iDisplay;	
//  	}
//  	return $p_iLowerLimit + $p_iDisplay;
//  }
  
  private function computeUpperLimit($p_iPageIndex, $p_iDisplay){
  	return $p_iDisplay * ($p_iPageIndex+1);
  }
  
  private function saveSearch($p_strUsername, $p_strKeywords, $p_strQuery, $p_oCreateDate){
    require 'lib/data/SearchLog.php';

    $oSearchLog = new SearchLog();
    $oSearchLog->setUsername($p_strUsername);
    $oSearchLog->setKeyword($p_strKeywords);
    $oSearchLog->setQuery($p_strQuery);
    $oSearchLog->setCreated($p_oCreateDate);
    $oSearchLog->save();
    return $oSearchLog;
  }
 
}
?>
