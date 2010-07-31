<?php

  require_once('util/DbHelper.php');
  require_once('util/DbParameter.php');
  require_once('util/DbStatement.php');
  require_once('neesconfiguration.php');

  class PhotoScaler{

    public static function findStructuredProjects(){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);

      $strQuery = "select p.projid from project p where p.project_type_id=:type order by p.projid";

      #bind query and variables
      $oDbStatement = new DbStatement();
      $oDbStatement->prepareStatement($strQuery);
      $oDbStatement->bind(":type", 2);

      #execute query
      $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
      DbHelper::close($oConnection);

      return $rowArray;
    }

    public static function findProjectImageCount($p_iProjectId){
      $oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
      //var_dump($oConnection);
      
      $strQuery = "select count(df.id) as total
                   from ".NeesConfig::ORACLE_SCHEMA.".data_file df,
                        ".NeesConfig::ORACLE_SCHEMA.".data_file_link dfl
                   where df.id = dfl.id
                     and df.deleted=0
                     and df.directory=0
                     and dfl.deleted=0
                     and dfl.proj_id=:nProjectId
                     and(
                          (lower(df.name) like '%.png') or
                          (lower(df.name) like '%.jpg') or
                          (lower(df.name) like '%.gif')
                   )";

      #bind query and variables
      $oDbStatement = new DbStatement();
      $oDbStatement->prepareStatement($strQuery);
      $oDbStatement->bind(":nProjectId", $p_iProjectId);

      #execute query
      $rowArray = DbHelper::executeQuery($oConnection, $oDbStatement);
      DbHelper::close($oConnection);

      if(empty($rowArray[0]['TOTAL']))return 0;

      return $rowArray[0]['TOTAL'];
    }

    public static function findImagesToScale($p_iProjectId, $p_iLowerLimit=1, $p_iUpperLimit=25){
        
    }

  }

?>
