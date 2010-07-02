<?php

  class DbHelper{
  	
  	/**
  	 * Get a connection to an oracle database
  	 * @param String $p_sUsername
  	 * @param String $p_sPassword
  	 * @param String $p_sDatabase
  	 * @return oci connection
  	 */
    public static function getConnection($p_sUsername, $p_sPassword, $p_sDatabase) {
      return oci_connect($p_sUsername, $p_sPassword, $p_sDatabase);
    }
    
    /**
     * Close an oracle database connection
     * @param oci connection $p_oConnection
     */
    public static function close($p_oConnection) {
      oci_close($p_oConnection);
    }
    
    /**
     * Return the results of an oracle query
     * @param oci connection $p_oConnection
     * @param array $p_oDbStatement
     * @return array of result rows
     * @see http://php.net/manual/en/function.oci-bind-by-name.php
     */
    public static function executeQuery($p_oConnection, $p_oDbStatement){
      $oReturnArray = array();

      $oStatement = oci_parse($p_oConnection, $p_oDbStatement->getQuery());

      #bind the parameter values to their respective placeholder to stop sql in$
      $oParameterArray = $p_oDbStatement->getParameters();
      foreach ($oParameterArray as $sKey => $oParameter) {
        oci_bind_by_name($oStatement, $oParameterArray[$sKey]->getName(), $oParameterArray[$sKey]->getValue());
        unset($oParameter);
      }

      oci_execute($oStatement);
      while ($oResultArray = oci_fetch_array($oStatement,OCI_BOTH)) {
        array_push($oReturnArray, $oResultArray);
      }

      return $oReturnArray;
    }
    
    public static function executeStatement($p_oConnection, $p_oDbStatement){
      $oStatement = oci_parse($p_oConnection, $p_oDbStatement->getQuery());

      #bind the parameter values to their respective placeholder to stop sql in$
      $oParameterArray = $p_oDbStatement->getParameters();
      foreach ($oParameterArray as $sKey => $oParameter) {
        oci_bind_by_name($oStatement, $oParameterArray[$sKey]->getName(), $oParameterArray[$sKey]->getValue());
        unset($oParameter);
      }

      oci_execute($oStatement);
      
      return $oStatement;
    }

    /**
     * Return the results of an oracle query
     * @param oci connection $p_oConnection
     * @param String $p_sQuery
     * @return array of result rows
     */
    public static function  select($p_oConnection, $p_sQuery){
      #return an array of database rows (array)
      $oReturnArray = array();
      
      #create statement object using connection and query
      $oStatement = oci_parse($p_oConnection, $p_sQuery);
      
      #execute query and process store results
      oci_execute($oStatement);
      while ($oResultArray = oci_fetch_array($oStatement,OCI_BOTH)) {
	    array_push($oReturnArray, $oResultArray);
      }	
      
      #return array of arrays
      return $oReturnArray;
    }
  	
    public static function executeUpdate($p_oConnection, $p_oDbStatement){
      $oStatement = oci_parse($p_oConnection, $p_oDbStatement->getQuery());
	  
      //bind the parameter values to their respective placeholder to stop sql injection
      $oParameterArray = $p_oDbStatement->getParameters();
      foreach ($oParameterArray as $sKey => $oParameter) {
        oci_bind_by_name($oStatement, $oParameterArray[$sKey]->getName(), $oParameterArray[$sKey]->getValue());
      }
  	  
      $bReturn = false;
      try{
      	$bReturn = oci_execute($oStatement, OCI_DEFAULT); 
        if($bReturn){
          oci_commit($p_oConnection);
        }else{
          oci_rollback($p_oConnection);// etc.
        }
      }catch(Exception $oException){
      	oci_rollback($p_oConnection);
      	return $bReturn;
      }
      
      oci_free_statement($oStatement);
      return $bReturn;
    }
    
    /**
     * Inserts or updates data in a batch process.  This is 
     * an all or nothing process.  If an error occurs within 
     * the middle of execution, the connection does a rollback.
     *   
     * @param $p_oConnection - open connection to database
     * @param $p_oDbStatementArray - array of DbStatements
     * @return $bReturn - true or false
     */
    public static function executeBatch($p_oConnection, $p_oDbStatementArray){
      $bReturn = true;
      
      foreach($p_oDbStatementArray as $oDbStatement){
//      	echo $oDbStatement->getQuery()."<br>";
	    $oStatement = oci_parse($p_oConnection, $oDbStatement->getQuery());
	    
	    //bind the parameter values to their respective placeholder to stop sql injection
        $oParameterArray = $oDbStatement->getParameters();
        foreach ($oParameterArray as $sKey => $oParameter) {
          oci_bind_by_name($oStatement, $oParameterArray[$sKey]->getName(), $oParameterArray[$sKey]->getValue());
        }//end foreach $oParameterArray
  	  
        try{
          $bExecuted = oci_execute($oStatement, OCI_DEFAULT);
//          echo "DbHelper::executeBatch - executed=$bExecuted<br>";
      	  if(!$bExecuted){
            $bReturn = false; 
          }
        }catch(Exception $oException){
      	  $bReturn = false;
        }//end try-catch
      
        oci_free_statement($oStatement);
      }//end foreach $p_oDbStatementArray
      
      //if $bReturn is still true, commit.  otherwise, rollback.
      if($bReturn){
        $bCommit = oci_commit($p_oConnection);
//        echo "DbHelper::executeBatch - commit=$bCommit<br>";
      }else{
      	$bRollback = oci_rollback($p_oConnection);
//      	echo "DbHelper::executeBatch - rollback=$bRollback<br>";
      }
      return $bReturn;
    }
    
  }

?>
