<?php 

  require_once('api/org/nees/oracle/util/DbHelper.php');
  require_once('api/org/nees/oracle/util/DbParameter.php');
  require_once('api/org/nees/oracle/util/DbStatement.php');
  require_once('neesconfiguration.php');

  class Experiment{
  	
  	/**
  	 * Find project by primary key.
  	 *
  	 */
    public static function getProjectById($p_nProjectId){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "select projid, name, title, short_title, to_char(DESCRIPTION) as DESCRIPTION, ".
  	  			"viewable, curation_status, status, start_date, end_date, contact_name ".
  		  	    "from ".NeesConfig::ORACLE_SCHEMA.".project ".
  			    "where projid = :nProjectId ";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nProjectId", $p_nProjectId);

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray)){
  	  	return $rowArray;
  	  }
  	  
  	  return $rowArray[0];
    }
    
    /**
  	 * Find experiment by primary key.
  	 *
  	 */
    public static function getExperimentById($p_nExperimentId){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "select expid, name, title, to_char(DESCRIPTION) as DESCRIPTION, ".
  	  			"viewable, curation_status, status, start_date, end_date ".
  		  	    "from ".NeesConfig::ORACLE_SCHEMA.".experiment ".
  			    "where expid = :nExperimentId ";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nExperimentId", $p_nExperimentId);

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray)){
  	  	return $rowArray;
  	  }
  	  
  	  return $rowArray[0];
    }
    
    /**
     * Find a list of projects by visibility and curation status.
     *
     */
    public static function getProjectsCountByCurationStatus($p_nDeleted, $p_sCurationStatus){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  $sQuery =	"SELECT count(projid) as num ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project ". 
  				"WHERE deleted=:nDeleted ".
    			"  and upper(curation_status)=:sCurationStatus ";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sCurationStatus", $p_sCurationStatus);

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray[0]['NUM']))
  	    return 0;
  	  
  	  return $rowArray[0]['NUM'];
    }//end getProjectsByCurationStatus
    
    public static function getProjectsByCurationStatusWithPagination($p_nDeleted, $p_sCurationStatus, $p_nCurrentIndex, $p_nDisplaySize){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  #computer lower and upper limits
  	  $nLowerLimit = $p_nCurrentIndex * $p_nDisplaySize;
  	  $nUpperLimit = ($p_nCurrentIndex+1) * $p_nDisplaySize;

	  /* 	
	   * NOTE:
	   * If the display size is 0, the user wants ALL rows.
	   * Thus, the upper limit will be zero, and we can't 	
	   * use the BETWEEN clause.  The search must use  the 
       * greater than row number clause. 	  
	   */
  	  $sQuery =	"SELECT * ". 
				"FROM( ". 
  				"  SELECT projid, name, title, viewable, curation_status, ".
  	  			"         contact_name, contact_email, row_number() ". 
  				"  OVER (order by name desc) rn ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project ". 
  				"  WHERE deleted=:nDeleted ".
    			        "    and upper(curation_status)=:sCurationStatus ".
				") ";
	  if($nUpperLimit != 0){ 
	    $sQuery = $sQuery . "WHERE rn BETWEEN :nLowerLimit and :nUpperLimit "; 
	  }else{
	    $sQuery = $sQuery . "WHERE rn > :nUpperLimit ";            
	  }
	  $sQuery = $sQuery .	"order by name desc";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sCurationStatus", $p_sCurationStatus);

	  /*
       * See note above about setting search boundaries.
       */
	  if($nUpperLimit != 0)$oDbStatement->bind(":nLowerLimit", $nLowerLimit);
  	  $oDbStatement->bind(":nUpperLimit", $nUpperLimit);
  	  
	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  return $rowArray;
    }//end getProjectsByCurationStatusWithPagination
    
    /**
     * Find all of the documents related to a specified project.
     * There are not any foreign keys that link the project to 
     * its rescpective data file.  The query is performed using 
     * a like clause to find all documents under the directory:
     * /nees/home/<project_name>.groups
     * @param $p_sProjectName - name of the given project
     * @param $p_nDeleted - 0 or 1 for not deleted or removed files.
     * @return collection of rows (array)
     */
    public static function getProjectDocumentsAll($p_sProjectName, $p_nDeleted){
      $sProjectFilePath = "/nees/home/".$p_sProjectName.".groups%";
      $sHasExtension = "%.%";
      
      $sRequestPath = "df.path";
      
      
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  $sQuery = "select df.id, df.name, df.path, df.created, to_char(df.DESCRIPTION) as DESCRIPTION, ".
  	  			"df.filesize, df.mime_type, df.thumb_id ".
  		  	    "from ".NeesConfig::ORACLE_SCHEMA.".data_file df ".
  			    "where df.path like :sPath ".
  	  			"  and df.deleted = :nDeleted ".
  	  			"  and df.name like :hasExtension ".
  	  			"order by df.path, df.name";
  	  
  	  //echo "query: ".$sQuery."<br>";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":sPath", $sProjectFilePath);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":hasExtension", $sHasExtension);
  	  
	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
      
  	  DbHelper::close($oConnection);
  	  return $rowArray;
    }//end getProjectDocumentsAll
    
    /**
     * Find a list of projects by deleted and curation name.
     *
     */
    public static function getProjectsCountByName($p_nDeleted, $p_sName){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  $sQuery =	"SELECT count(projid) as num ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project ". 
  				"WHERE deleted=:nDeleted ".
    			"  and upper(name) like :sName ";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sName", "%".strtoupper($p_sName)."%");

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray[0]['NUM']))
  	    return 0;
  	  
  	  return $rowArray[0]['NUM'];
    }//end getProjectsByCurationStatus
    
    /**
     * 
     *
     */
    public static function getProjectsByNameWithPagination($p_sName, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  #computer lower and upper limits
  	  $nLowerLimit = $p_nCurrentIndex * $p_nDisplaySize;
  	  $nUpperLimit = ($p_nCurrentIndex+1) * $p_nDisplaySize;
  	  
  	  /* 	
	   * NOTE:
	   * If the display size is 0, the user wants ALL rows.
	   * Thus, the upper limit will be zero, and we can't 	
	   * use the BETWEEN clause.  The search must use  the 
       * greater than row number clause. 	  
	   */
  	  $sQuery =	"SELECT * ". 
				"FROM( ". 
  				"  SELECT projid, name, title, viewable, curation_status, ".
  	  			"         contact_name, contact_email, row_number() ". 
  				"  OVER (order by name desc) rn ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project ". 
  				"  WHERE deleted=:nDeleted ".
    			        "    and upper(name) like :sName ".
				") ";
	  if($nUpperLimit != 0){ 
	    $sQuery = $sQuery . "WHERE rn BETWEEN :nLowerLimit and :nUpperLimit "; 
	  }else{
	    $sQuery = $sQuery . "WHERE rn > :nUpperLimit ";            
	  }
	  $sQuery = $sQuery .	"order by name desc";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sName", "%".$p_sName."%");

	  /*
       * See note above about setting search boundaries.
       */
	  if($nUpperLimit != 0)$oDbStatement->bind(":nLowerLimit", $nLowerLimit);
  	  $oDbStatement->bind(":nUpperLimit", $nUpperLimit);
  	  
	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  return $rowArray;	
    }//getProjectsByNameWithPagination
    
    /**
     * Find a list of projects by deleted and title.
     *
     */
    public static function getProjectsCountByTitle($p_nDeleted, $p_sTitle){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  $sQuery =	"SELECT count(projid) as num ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project ". 
  				"WHERE deleted=:nDeleted ".
    			"  and upper(title) like :sTitle ";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sTitle", "%".strtoupper($p_sTitle)."%");

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray[0]['NUM']))
  	    return 0;
  	  
  	  return $rowArray[0]['NUM'];
    }//end getProjectsCountByTitle
    
    /**
     * 
     *
     */
    public static function getProjectsByTitleWithPagination($p_sTitle, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  #computer lower and upper limits
  	  $nLowerLimit = $p_nCurrentIndex * $p_nDisplaySize;
  	  $nUpperLimit = ($p_nCurrentIndex+1) * $p_nDisplaySize;

  	  /* 	
	   * NOTE:
	   * If the display size is 0, the user wants ALL rows.
	   * Thus, the upper limit will be zero, and we can't 	
	   * use the BETWEEN clause.  The search must use  the 
       * greater than row number clause. 	  
	   */
  	  $sQuery =	"SELECT * ". 
				"FROM( ". 
  				"  SELECT projid, name, title, viewable, curation_status, ".
  	  			"         contact_name, contact_email, row_number() ". 
  				"  OVER (order by name desc) rn ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project ". 
  				"  WHERE deleted=:nDeleted ".
    			        "    and upper(title) like :sTitle ".
				") ";
	  if($nUpperLimit != 0){ 
	    $sQuery = $sQuery . "WHERE rn BETWEEN :nLowerLimit and :nUpperLimit "; 
	  }else{
	    $sQuery = $sQuery . "WHERE rn > :nUpperLimit ";            
	  }
	  $sQuery = $sQuery .	"order by name desc";
	  
	  //echo "title query=".$sQuery."<br>";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sTitle", "%".strtoupper($p_sTitle)."%");

	  /*
       * See note above about setting search boundaries.
       */
	  if($nUpperLimit != 0)$oDbStatement->bind(":nLowerLimit", $nLowerLimit);
  	  $oDbStatement->bind(":nUpperLimit", $nUpperLimit);
  	  
	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  return $rowArray;	
    }//getProjectsByTitleWithPagination
    
    /**
     * Find a list of projects by deleted and description.
     *
     */
    public static function getProjectsCountByDescription($p_nDeleted, $p_sDescription){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  $sQuery =	"SELECT count(projid) as num ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project ". 
  				"WHERE deleted=:nDeleted ".
    			"  and upper(description) like :sDescription ";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sDescription", "%".strtoupper($p_sDescription)."%");

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray[0]['NUM']))
  	    return 0;
  	  
  	  return $rowArray[0]['NUM'];
    }//end getProjectsCountByDescription
    
    /**
     * 
     *
     */
    public static function getProjectsByDescriptionWithPagination($p_sDescription, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	  
  	  #computer lower and upper limits
  	  $nLowerLimit = $p_nCurrentIndex * $p_nDisplaySize;
  	  $nUpperLimit = ($p_nCurrentIndex+1) * $p_nDisplaySize;

	  /* 	
	   * NOTE:
	   * If the display size is 0, the user wants ALL rows.
	   * Thus, the upper limit will be zero, and we can't 	
	   * use the BETWEEN clause.  The search must use  the 
       * greater than row number clause. 	  
	   */
  	  $sQuery =	"SELECT * ". 
				"FROM( ". 
  				"  SELECT projid, name, title, viewable, curation_status, ".
  	  			"         contact_name, contact_email, row_number() ". 
  				"  OVER (order by name desc) rn ". 
  				"from ".NeesConfig::ORACLE_SCHEMA.".project ". 
  				"  WHERE deleted=:nDeleted ".
    			"    and upper(description) like :sDescription ".
				") ";
	  if($nUpperLimit != 0){ 
	    $sQuery = $sQuery . "WHERE rn BETWEEN :nLowerLimit and :nUpperLimit "; 
	  }else{
	    $sQuery = $sQuery . "WHERE rn > :nUpperLimit ";            
	  }
	  $sQuery = $sQuery .	"order by name desc";
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($sQuery);
  	  $oDbStatement->bind(":nDeleted", $p_nDeleted);
  	  $oDbStatement->bind(":sDescription", "%".strtoupper($p_sDescription)."%");

	  /*
       * See note above about setting search boundaries.
       */
	  if($nUpperLimit != 0)$oDbStatement->bind(":nLowerLimit", $nLowerLimit);
  	  $oDbStatement->bind(":nUpperLimit", $nUpperLimit);
  	  
	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  return $rowArray;	
    }//getProjectsByDescriptionWithPagination
    
  	
  }//end Project.class

?>
