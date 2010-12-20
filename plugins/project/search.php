<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.event.plugin');

class plgProjectSearch extends JPlugin{

  private $m_iLowerLimit;
  private $m_iUpperLimit;

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
   * Performs the warehouse search.
   * @global <type> $mainframe
   * @param <type> $params
   * @return array
   */
  function onProjectSearch(&$params){
    global $mainframe;

    $strQuery = $this->getSearchQuery();
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

    JRequest::setVar('low', $this->m_iLowerLimit);
    JRequest::setVar('high', $this->m_iUpperLimit);

    return $strResultsArray;
  }

  /**
    * Plugin method with the same name as the event will be called automatically.
    */
  function onProjectSearchCount(&$params){
    global $mainframe;

    return ProjectPeer::searchByFormCount($this->getSearchCountQuery(), "TOTAL");
  }

  /**
   * Get the query to use for the search.  If the keywords parameter
   * is set, union three queries for depth search.  The first union captures
   * the project.  Next, we join the experiments.  Finally, the trials
   * are joined.
   *
   * If keywords is not set, just search the project.
   * @return string
   * @since 20101012
   */
  private function getSearchQuery(){
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

    $strQuery = "";
    if(StringHelper::hasText($strKeywords)){
      $strQuery = $this->buildKeywordQuery($strKeywords, $strType, $strFunding,
                                           $strMember, $strStartDate, $strEndDate,
                                           $strOrderBy, $iLimitStart, $iDisplay,
                                           $iPageIndex);
    }else{
      $strQuery = $this->buildQuery($strType, $strFunding,
                                   $strMember, $strStartDate, $strEndDate,
                                   $strOrderBy, $iLimitStart, $iDisplay,
                                   $iPageIndex);
    }

    //echo $strQuery."<br>";

    return $strQuery;
  }

  /**
   * Get the number of records for the query.
   * @return string
   * @since 20101012
   */
  private function getSearchCountQuery(){
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

    $strQuery = "";
    if(StringHelper::hasText($strKeywords)){
      $strQuery = $this->buildKeywordCountQuery($strKeywords, $strType, $strFunding,
                                           $strMember, $strStartDate, $strEndDate,
                                           $strOrderBy);
    }else{
      $strQuery = $this->buildCountQuery($strType, $strFunding, $strMember,
                                         $strStartDate, $strEndDate, $strOrderBy);
    }

    //echo $strQuery."<br>";

    return $strQuery;
  }

  /**
   * Lookup the current user's oracle id.
   * @return int
   * @since 20101012
   */
  private function getPersonId(){
    $iPersonId = 0;

    $oJuser =& JFactory::getUser();
    if(!$oJuser->guest){
      $oPerson = PersonPeer::findByUserName($oJuser->username);
      if($oPerson){
        $iPersonId = $oPerson->getId();
      }
    }

    return $iPersonId;
  }

  /**
   * Returns the keyword query consisting of three unions.
   * @param string $p_strKeywords
   * @param string $p_strType
   * @param string $p_strFunding
   * @param string $p_strMember
   * @param string $p_strStartDate
   * @param string $p_strEnd
   * @param string $p_strOrderBy
   * @param int $p_iLimitStart
   * @param int $p_iDisplay
   * @param int $p_iPageIndex
   * @return string
   * @since 20101012
   */
  private function buildKeywordQuery($p_strKeywords, $p_strType, $p_strFunding,
                                     $p_strMember, $p_strStartDate, $p_strEnd,
                                     $p_strOrderBy, $p_iLimitStart, $p_iDisplay,
                                     $p_iPageIndex){

    $iLowerLimit = $this->computeLowerLimit($p_iPageIndex, $p_iDisplay);
    $iUpperLimit = $this->computeUpperLimit($p_iPageIndex, $p_iDisplay);

    $this->m_iLowerLimit = $iLowerLimit;
    $this->m_iUpperLimit = $iUpperLimit;

    $strQueryPrefix = "SELECT distinct projid, nickname, start_date ".
                      "FROM ( ".
                            "SELECT projid, nickname, start_date, row_number() ".
                            "OVER (ORDER BY $p_strOrderBy) as rn ".
                            "FROM( ";
    $strQuerySuffix = ") ".
                      ")WHERE rn BETWEEN ".$this->m_iLowerLimit." AND ".$this->m_iUpperLimit." ".
                      "ORDER BY $p_strOrderBy";


    //build the project conditions
    $iProjectId = $this->getPersonId();
    $strProjectMemberCondition = $this->getProjectMemberCondition($p_strMember);
    $strProjectGrantCondition = $this->getProjectGrantConditions($p_strFunding);
    $strProjectStartDateCondition = $this->getProjectStartDateCondition($p_strStartDate);
    $strProjectEndDateCondition = $this->getProjectEndDateCondition($p_strStartDate);

    //build the project query (1/3 union statements)
    $strProjectQuery = $this->getProjectQuery($p_strKeywords, $iProjectId,
                                              $strProjectMemberCondition, $strProjectGrantCondition,
                                              $strProjectStartDateCondition, $strProjectEndDateCondition);

    //build the experiment query (2/3 union statements)
    $strExperimentQuery = $this->getExperimentQuery($p_strKeywords, $iProjectId,
                                              $strProjectMemberCondition, $strProjectGrantCondition,
                                              $strProjectStartDateCondition, $strProjectEndDateCondition);

    //build the trial query (3/3 union statements)
    $strTrialQuery = $this->getTrialQuery($p_strKeywords, $iProjectId,
                                              $strProjectMemberCondition, $strProjectGrantCondition,
                                              $strProjectStartDateCondition, $strProjectEndDateCondition);

    //concatenate the query prefix, unions, and suffix
    if(StringHelper::hasText($strProjectQuery) &&
       StringHelper::hasText($strExperimentQuery) &&
       StringHelper::hasText($strTrialQuery)){

      $strQueryUnions = $strProjectQuery." UNION ".$strExperimentQuery." UNION ".$strTrialQuery;
      $strQuery = $strQueryPrefix ." ".$strQueryUnions." ".$strQuerySuffix;
    }

    //echo $strQuery."<br>";

    return $strQuery;
  }

  /**
   * Returns a total count of records that match the keyword search.
   * @param string $p_strKeywords
   * @param string $p_strType
   * @param string $p_strFunding
   * @param string $p_strMember
   * @param string $p_strStartDate
   * @param string $p_strEnd
   * @param string $p_strOrderBy
   * @return string
   * @since 20101012
   */
  private function buildKeywordCountQuery($p_strKeywords, $p_strType, $p_strFunding,
                                     $p_strMember, $p_strStartDate, $p_strEnd,
                                     $p_strOrderBy){

    $strQueryPrefix = "SELECT count(distinct projid) as TOTAL
                       FROM ( ";

    $strQuerySuffix = ")";


    //build the project conditions
    $iProjectId = $this->getPersonId();
    $strProjectMemberCondition = $this->getProjectMemberCondition($p_strMember);
    $strProjectGrantCondition = $this->getProjectGrantConditions($p_strFunding);
    $strProjectStartDateCondition = $this->getProjectStartDateCondition($p_strStartDate);
    $strProjectEndDateCondition = $this->getProjectEndDateCondition($p_strStartDate);

    //build the project query (1/3 union statements)
    $strProjectQuery = $this->getProjectQuery($p_strKeywords, $iProjectId,
                                              $strProjectMemberCondition, $strProjectGrantCondition,
                                              $strProjectStartDateCondition, $strProjectEndDateCondition);

    //build the experiment query (2/3 union statements)
    $strExperimentQuery = $this->getExperimentQuery($p_strKeywords, $iProjectId,
                                              $strProjectMemberCondition, $strProjectGrantCondition,
                                              $strProjectStartDateCondition, $strProjectEndDateCondition);

    //build the trial query (3/3 union statements)
    $strTrialQuery = $this->getTrialQuery($p_strKeywords, $iProjectId,
                                              $strProjectMemberCondition, $strProjectGrantCondition,
                                              $strProjectStartDateCondition, $strProjectEndDateCondition);

    //concatenate the query prefix, unions, and suffix
    if(StringHelper::hasText($strProjectQuery) &&
       StringHelper::hasText($strExperimentQuery) &&
       StringHelper::hasText($strTrialQuery)){

      $strQueryUnions = $strProjectQuery." UNION ".$strExperimentQuery." UNION ".$strTrialQuery;
      $strQuery = $strQueryPrefix ." ".$strQueryUnions." ".$strQuerySuffix;
    }

    return $strQuery;
  }

  /**
   * Performs a project search without any keywords.
   * @param string $p_strType
   * @param string $p_strFunding
   * @param string $p_strMember
   * @param string $p_strStartDate
   * @param string $p_strEnd
   * @param string $p_strOrderBy
   * @param int $p_iLimitStart
   * @param int $p_iDisplay
   * @param int $p_iPageIndex
   * @return string
   */
  private function buildQuery($p_strType, $p_strFunding,
                             $p_strMember, $p_strStartDate, $p_strEnd,
                             $p_strOrderBy, $p_iLimitStart, $p_iDisplay,
                             $p_iPageIndex){

    $iLowerLimit = $this->computeLowerLimit($p_iPageIndex, $p_iDisplay);
    $iUpperLimit = $this->computeUpperLimit($p_iPageIndex, $p_iDisplay);

    $this->m_iLowerLimit = $iLowerLimit;
    $this->m_iUpperLimit = $iUpperLimit;

    $strQueryPrefix = "SELECT distinct projid, nickname, start_date ".
                      "FROM ( ".
                             "SELECT projid, nickname, start_date, row_number() ".
                             "OVER (ORDER BY $p_strOrderBy) as rn ".
                             "FROM(";
    $strQuerySuffix = " ) ".
                      ")WHERE rn BETWEEN ".$this->m_iLowerLimit." AND ".$this->m_iUpperLimit.
                      " ORDER BY $p_strOrderBy";

    //build the project conditions
    $iPersonId = $this->getPersonId();
    $strProjectMemberCondition = $this->getProjectMemberCondition($p_strMember);
    $strProjectGrantCondition = $this->getProjectGrantConditions($p_strFunding);
    $strProjectStartDateCondition = $this->getProjectStartDateCondition($p_strStartDate);
    $strProjectEndDateCondition = $this->getProjectEndDateCondition($p_strStartDate);

    $strThisQuery = $this->getQuery($iPersonId, $strProjectMemberCondition, $strProjectGrantCondition,
                                    $strProjectStartDateCondition, $strProjectEndDateCondition);

    //concatenate the query prefix, unions, and suffix
    if(StringHelper::hasText($strThisQuery)){
      $strQuery = $strQueryPrefix ." ".$strThisQuery." ".$strQuerySuffix;
    }

    //echo $strQuery."<br>";

    return $strQuery;
  }

  /**
   * Returns the count of projects found without a keyword based search.
   * @param string $p_strType
   * @param string $p_strFunding
   * @param string $p_strMember
   * @param string $p_strStartDate
   * @param string $p_strEnd
   * @param string $p_strOrderBy
   * @return string
   */
  private function buildCountQuery($p_strType, $p_strFunding,
                                   $p_strMember, $p_strStartDate, $p_strEnd,
                                   $p_strOrderBy){

    $strQueryPrefix = "SELECT count(distinct projid) as TOTAL
                       FROM (
                             SELECT projid, nickname, start_date, row_number()
                             OVER (ORDER BY $p_strOrderBy) as rn
                             FROM(";
    $strQuerySuffix = "      )
                       )";


    //build the project conditions
    $iPersonId = $this->getPersonId();
    $strProjectMemberCondition = $this->getProjectMemberCondition($p_strMember);
    $strProjectGrantCondition = $this->getProjectGrantConditions($p_strFunding);
    $strProjectStartDateCondition = $this->getProjectStartDateCondition($p_strStartDate);
    $strProjectEndDateCondition = $this->getProjectEndDateCondition($p_strStartDate);

    $strQuery = $this->getCountQuery($iPersonId, $strProjectMemberCondition, $strProjectGrantCondition,
                                        $strProjectStartDateCondition, $strProjectEndDateCondition);

    return $strQuery;
  }

  /**
   * Returns the where/on keyword condition.  Currently, the keywords are compared
   * to the $p_strTableName's title or description fields
   * @param string $p_strTerms
   * @param string $p_strTableName
   * @return string
   * @since 20101012
   */
  private function getKeywordDescriptionTitleConditions($p_strTerms, $p_strTableName){
    $strKeywordConditions = "";
    $strTermArray = split(" ", $p_strTerms);
    if(!empty($strTermArray)){
      $strKeywordConditions .= "(";

      /*
       * Match on each of the entered terms.
       */
      foreach($strTermArray as $iTermIndex=>$strTerm){
        $strThisTerm = "'%".strtolower($strTerm)."%'";
        $strKeywordConditions .= "(lower(".$p_strTableName.".description) like $strThisTerm or lower(".$p_strTableName.".title) like $strThisTerm)";
        $strKeywordConditions .= $this->appendAndOr($iTermIndex, $strTermArray, "OR");
      }

      $strKeywordConditions .= ")";
    }
    return $strKeywordConditions;
  }

  /**
   * Returns a string containing a logical operator (AND/OR) if the given
   * array has more elements.
   * @param int $p_iIndex
   * @param array $strArray
   * @param string $p_strLogicalOperator
   * @return string
   * @since 20101012
   * @see getKeywordDescriptionTitleConditions
   */
  private function appendAndOr($p_iIndex, $strArray, $p_strLogicalOperator){
    if($p_iIndex < count($strArray)-1){
      return " ".$p_strLogicalOperator." ";
    }
    return "";
  }

  /**
   * Returns a join to search if the entered person is a member of the project.
   * @param string $p_strMemberName
   * @return string
   * @since 20101012
   */
  private function getProjectMemberCondition($p_strMemberName){
    if(!StringHelper::hasText($p_strMemberName) || $p_strMemberName =="Last Name, First Name" ){
      return "";
    }

    $strLastName = StringHelper::EMPTY_STRING;
    $strFirstName = StringHelper::EMPTY_STRING;

    $strMemberNameArray = split(",", $p_strMemberName);
    $strLastName = trim(strtolower($strMemberNameArray[0]));
    if(count($strMemberNameArray) > 1){
      $strFirstName = trim(strtolower($strMemberNameArray[1]));
    }

    $strMemberCondition = "inner join ".PersonEntityRolePeer::TABLE_NAME.
                          " on ".ProjectPeer::PROJID." = ".PersonEntityRolePeer::ENTITY_ID.
                          " and ".PersonEntityRolePeer::ENTITY_TYPE_ID."=1".
                          " inner join ".PersonPeer::TABLE_NAME.
                          " on ".PersonEntityRolePeer::PERSON_ID." = ".PersonPeer::ID.
                          " and lower(".PersonPeer::LAST_NAME.") like '$strLastName%' ";
    if(StringHelper::hasText($strFirstName)){
      $strMemberCondition .= "and lower(".PersonPeer::FIRST_NAME.") like '$strFirstName%' ";;
    }



    return $strMemberCondition;
  }

  /**
   * Returns a join to find projects with the given sponsor.
   * @param string $p_strFunding
   * @return string
   * @since 20101012
   */
  function getProjectGrantConditions($p_strFunding){
    if(!$p_strFunding || $p_strFunding == ""){
      return "";
    }else{
      $p_strFunding = strtolower($p_strFunding);
    }

    $strFundCondition = "inner join ".ProjectGrantPeer::TABLE_NAME."
                         on ".ProjectGrantPeer::PROJID." = ".ProjectPeer::PROJID."
                         and lower(".ProjectGrantPeer::FUND_ORG.") like '%$p_strFunding%'";
    return $strFundCondition;
  }

  /**
   * Returns the where condition for the project start date.
   * @param string $p_strStartDate
   * @return string
   * @since 20101012
   */
  private function getProjectStartDateCondition($p_strStartDate){
    if(!StringHelper::hasText($p_strStartDate) || $p_strStartDate == "mm/dd/yyyy"){
      return "";
    }

    return " AND ".ProjectPeer::START_DATE." >= to_date('$p_strStartDate', 'MM/dd/yyyy')";
  }

  /**
   * Returns the where condition for the project end date.
   * @param string $p_strEndDate
   * @return string
   * @since 20101012
   */
  private function getProjectEndDateCondition($p_strEndDate){
    if(!StringHelper::hasText($p_strEndDate) || $p_strEndDate == "mm/dd/yyyy"){
      return "";
    }

    return " AND ".ProjectPeer::START_DATE." <= to_date('$p_strEndDate', 'MM/dd/yyyy')";
  }

  /**
   * Returns the first keyword query in a set of three.  This query only searches
   * the project.
   * @param string $p_strTerms
   * @param int $p_iPersonId
   * @param string $p_strMemberCondition
   * @param string $p_strFundingCondition
   * @param string $p_strStartCondition
   * @param string $p_strEndCondition
   * @since 20101012
   */
  private function getProjectQuery($p_strTerms, $p_iPersonId, $p_strMemberCondition, $p_strFundingCondition, $p_strStartCondition, $p_strEndCondition){
    $strKeywordConditions = $this->getKeywordDescriptionTitleConditions($p_strTerms, ProjectPeer::TABLE_NAME);

    $strThisQuery = "select ".ProjectPeer::PROJID.", ".ProjectPeer::NICKNAME.", ".ProjectPeer::START_DATE." ".
                    "from ".ProjectPeer::TABLE_NAME." ".
                    "left outer join ".AuthorizationPeer::TABLE_NAME." ".
                    "on ".ProjectPeer::PROJID." = ".AuthorizationPeer::ENTITY_ID." ".
                    "and ".AuthorizationPeer::ENTITY_TYPE_ID." = 1 ".
                    "and ".AuthorizationPeer::PERSON_ID ." = $p_iPersonId ".
                    "$p_strMemberCondition ".
                    "$p_strFundingCondition ".
                    "where ".ProjectPeer::DELETED."=0 ".
                      "and (".AuthorizationPeer::ID." is not null or ".ProjectPeer::VIEWABLE."='PUBLIC') ".
                      "and $strKeywordConditions ".
                      "$p_strStartCondition ".
                      "$p_strEndCondition";

    return $strThisQuery;
  }

  /**
   * Returns the second keyword query in a set of three.  This query joins the experiment
   * to the project.
   * @param string $p_strTerms
   * @param int $p_iPersonId
   * @param string $p_strMemberCondition
   * @param string $p_strFundingCondition
   * @param string $p_strStartCondition
   * @param string $p_strEndCondition
   * @since 20101012
   */
  private function getExperimentQuery($p_strTerms, $p_iPersonId, $p_strMemberCondition, $p_strFundingCondition, $p_strStartCondition, $p_strEndCondition){
    $strKeywordConditions = $this->getKeywordDescriptionTitleConditions($p_strTerms, ExperimentPeer::TABLE_NAME);

    $strThisQuery = "select ".ProjectPeer::PROJID.", ".ProjectPeer::NICKNAME.", ".ProjectPeer::START_DATE." ".
                    "from ".ProjectPeer::TABLE_NAME." ".
                    "inner join ".ExperimentPeer::TABLE_NAME." ".
                    "on ".ExperimentPeer::PROJID." = ".ProjectPeer::PROJID." ".
                    "and ".ExperimentPeer::DELETED."=0 ".
                    "and $strKeywordConditions ".
                    "$p_strMemberCondition ".
                    "$p_strFundingCondition ".
                    "left outer join ".AuthorizationPeer::TABLE_NAME." ".
                    "on ".ProjectPeer::PROJID." = ".AuthorizationPeer::ENTITY_ID." ".
                    "and ".AuthorizationPeer::ENTITY_TYPE_ID." = 1 ".
                    "and ".AuthorizationPeer::PERSON_ID ." = $p_iPersonId ".
                    "where ".ProjectPeer::DELETED."=0 ".
                      "and (".AuthorizationPeer::ID." is not null or ".ProjectPeer::VIEWABLE."='PUBLIC') ".
                      "$p_strStartCondition ".
                      "$p_strEndCondition";

    return $strThisQuery;
  }

  /**
   * Returns the third keyword query in a set of three.  This query joins the trial
   * to the experiment, and the experiment to the project.
   * @param string $p_strTerms
   * @param int $p_iPersonId
   * @param string $p_strMemberCondition
   * @param string $p_strFundingCondition
   * @param string $p_strStartCondition
   * @param string $p_strEndCondition
   * @return string
   * @since 20101012
   */
  private function getTrialQuery($p_strTerms, $p_iPersonId, $p_strMemberCondition, $p_strFundingCondition, $p_strStartCondition, $p_strEndCondition){
    $strKeywordConditions = $this->getKeywordDescriptionTitleConditions($p_strTerms, TrialPeer::TABLE_NAME);

    $strThisQuery = "select ".ProjectPeer::PROJID.", ".ProjectPeer::NICKNAME.", ".ProjectPeer::START_DATE." ".
                    "from ".ProjectPeer::TABLE_NAME." ".
                    "inner join ".ExperimentPeer::TABLE_NAME." ".
                    "on ".ExperimentPeer::PROJID." = ".ProjectPeer::PROJID." ".
                    "and ".ExperimentPeer::DELETED."=0 ".
                    "inner join ".TrialPeer::TABLE_NAME." ".
                    "on ".TrialPeer::EXPID." = ".ExperimentPeer::EXPID." ".
                    "and ".TrialPeer::DELETED."=0 ".
                    "and $strKeywordConditions ".
                    "$p_strMemberCondition ".
                    "$p_strFundingCondition ".
                    "left outer join ".AuthorizationPeer::TABLE_NAME." ".
                    "on ".ProjectPeer::PROJID." = ".AuthorizationPeer::ENTITY_ID." ".
                    "and ".AuthorizationPeer::ENTITY_TYPE_ID." = 1 ".
                    "and ".AuthorizationPeer::PERSON_ID ." = $p_iPersonId ".
                    "where ".ProjectPeer::DELETED."=0 ".
                      "and (".AuthorizationPeer::ID." is not null or ".ProjectPeer::VIEWABLE."='PUBLIC') ".
                      "$p_strStartCondition ".
                      "$p_strEndCondition";

    return $strThisQuery;
  }

  /**
   * Returns a non-keyword query
   * @param int $p_iPersonId
   * @param string $p_strMemberCondition
   * @param string $p_strFundingCondition
   * @param string $p_strStartCondition
   * @param string $p_strEndCondition
   * @return string
   * @see buildQuery()
   */
  private function getQuery($p_iPersonId, $p_strMemberCondition, $p_strFundingCondition, $p_strStartCondition, $p_strEndCondition){
    $strThisQuery = "select ".ProjectPeer::PROJID.", ".ProjectPeer::NICKNAME.", ".ProjectPeer::START_DATE."
                     from ".ProjectPeer::TABLE_NAME."
                     left outer join ".AuthorizationPeer::TABLE_NAME."
                     on ".ProjectPeer::PROJID." = ".AuthorizationPeer::ENTITY_ID."
                     and ".AuthorizationPeer::ENTITY_TYPE_ID." = 1
                     and ".AuthorizationPeer::PERSON_ID ." = $p_iPersonId
                     $p_strMemberCondition
                     $p_strFundingCondition
                     where ".ProjectPeer::DELETED."=0
                       and (".AuthorizationPeer::ID." is not null or ".ProjectPeer::VIEWABLE."='PUBLIC')
                       $p_strStartCondition
                       $p_strEndCondition";

    return $strThisQuery;
  }

  /**
   * Returns a total for a non-keyword query.
   * @param int $p_iPersonId
   * @param string $p_strMemberCondition
   * @param string $p_strFundingCondition
   * @param string $p_strStartCondition
   * @param string $p_strEndCondition
   * @return string
   */
  private function getCountQuery($p_iPersonId, $p_strMemberCondition, $p_strFundingCondition, $p_strStartCondition, $p_strEndCondition){
    $strThisQuery = "select count (distinct ".ProjectPeer::PROJID.") as TOTAL
                     from ".ProjectPeer::TABLE_NAME."
                     left outer join ".AuthorizationPeer::TABLE_NAME."
                     on ".ProjectPeer::PROJID." = ".AuthorizationPeer::ENTITY_ID."
                     and ".AuthorizationPeer::ENTITY_TYPE_ID." = 1
                     and ".AuthorizationPeer::PERSON_ID ." = $p_iPersonId
                     $p_strMemberCondition
                     $p_strFundingCondition
                     where ".ProjectPeer::DELETED."=0
                       and (".AuthorizationPeer::ID." is not null or ".ProjectPeer::VIEWABLE."='PUBLIC')
                       $p_strStartCondition
                       $p_strEndCondition";

    return $strThisQuery;
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

  /**
   * Finds the upper limit for a search.
   * @param int $p_iPageIndex
   * @param int $p_iDisplay
   * @return int
   */
  private function computeLowerLimit($p_iPageIndex, $p_iDisplay){
    if($p_iPageIndex==0){
      return 1;
    }
    return ($p_iPageIndex * $p_iDisplay) + 1;
  }

  /**
   * Finds the lower limit for a search.
   * @param int $p_iPageIndex
   * @param int $p_iDisplay
   * @return int
   */
  private function computeUpperLimit($p_iPageIndex, $p_iDisplay){
    return $p_iDisplay * ($p_iPageIndex+1);
  }

  /**
   * Stores the query in oracle
   * @param string $p_strUsername
   * @param string $p_strKeywords
   * @param string $p_strQuery
   * @param string $p_oCreateDate
   * @return SearchLog
   */
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
