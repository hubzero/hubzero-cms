<?php 

require_once('api/org/nees/oracle/util/DbHelper.php');
require_once('api/org/nees/oracle/util/DbParameter.php');
require_once('api/org/nees/oracle/util/DbStatement.php');
require_once('neesconfiguration.php');

class Suggest{
  	
  public static function getFacilityList($p_strName){
  	$p_strName = $p_strName."%";
  	
  	$oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
  	$strQuery = "select orgid, name from ".NeesConfig::ORACLE_SCHEMA.".Organization where name like :name or short_name like :short_name";
  	
  	#bind query and variables
  	$oDbStatement = new DbStatement();
  	$oDbStatement->prepareStatement($strQuery);
  	$oDbStatement->bind(":name", $p_strName);
  	$oDbStatement->bind(":short_name", $p_strName);

  	#execute query
  	$rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
  	DbHelper::close($oConnection);
  	  
  	if(empty($rowArray)){
  	  return $rowArray;
  	}
  	  
    return $rowArray;
  }  	
  	
}

?>