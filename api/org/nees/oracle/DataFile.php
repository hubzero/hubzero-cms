<?php 

  require_once('api/org/nees/oracle/util/DbHelper.php');
  require_once('api/org/nees/oracle/util/DbParameter.php');
  require_once('api/org/nees/oracle/util/DbStatement.php');
  require_once('api/org/nees/html/CurateHtml.php');
  require_once('neesconfiguration.php');
  
  class DataFile{
  	
  	public static function getById($p_iDataFileId){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);

  	  $strQuery = "select df.id, df.name, df.path, to_char(df.created,'mm-dd-yyyy') as created, to_char(df.description), df.title, df.viewable ".
  	  			  "from ".NeesConfig::ORACLE_SCHEMA.".data_file df ".
  	  			  "where df.id=:iDataFileId"; 
  	  
  	  #bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($strQuery);
  	  $oDbStatement->bind("iDataFileId", $p_iDataFileId);

	  #execute query
  	  $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  if(empty($rowArray)){
  	  	return $rowArray;
  	  }
  	  
  	  return $rowArray[0];
  	}
  	
  	/**
  	 * Find a collection of data files given an array of data file identifiers.
  	 * @param $p_iDataFileIdArray - data file ids.
  	 */
  	public static function getDataFiles($p_iDataFileIdArray){
  	  $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);

  	  $strQuery = "select id, name, path, created, authors, description, title, viewable, thumb_id, directory ".
  	  			  "from ".NeesConfig::ORACLE_SCHEMA.".data_file ". 
  	  			  "where deleted=:iDeleted ". 
  	  			  "  and id in (";
  	  
  	  //create bind variable names
  	  foreach($p_iDataFileIdArray as $iDataFileId){
  	  	$strQuery = $strQuery . ":iDataFile".$iDataFileId.",";
  	  }
  	  
  	  //remove last comma
  	  $strQuery = substr($strQuery, 0, strlen($strQuery)-1);
  	  
  	  //close the in clause
  	  $strQuery = $strQuery . ") ";
  	  
  	  //bind query and variables
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($strQuery);
  	  $oDbStatement->bind(":iDeleted", 0);
  	  foreach($p_iDataFileIdArray as $iDataFileId){
  	    $oDbStatement->bind(":iDataFile".$iDataFileId, $iDataFileId);
  	  }

	  //execute query
  	  $oRowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	  DbHelper::close($oConnection);
  	  
  	  return $oRowArray;
  	}
  	
  }

?>