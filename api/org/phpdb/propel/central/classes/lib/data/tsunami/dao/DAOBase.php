<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLDAOBase.php
**    Last Modification by: Ben Steinberg
**    Last Modified date: 2/20/06
**    Copyright © 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

class MySQLDAOBase
{
    protected $dblink=0;
    protected $Error=null;
    protected $DAOtype = "MySQL";
    protected $quoteables = array("char","varchar","enum","date","text");

    #   These need to be set by the subclass
    protected $Name = null;
    protected $metadata = null;

    #
    #   Database Access functions
    #
    protected function DBConnect()
    {
        global $ini_array;
        if (! $this->dblink ) {
            if ( ($this->dblink = mysql_pconnect("localhost", 
                                                $ini_array["centraldbuser"], 
                                                $ini_array["centraldbpass"])) == FALSE)

                return($this->setError("Failed to connect to DB"));


          mysql_select_db($ini_array["centraldbname"], $this->dblink);
        }

    }

    public function queryMyDAO($sql=null)
    {
        if (is_null($sql))
            return($this->setError("NULL query string submitted"));

        if (! $this->dblink ) 
            $this->DBConnect();

          if (($result = mysql_query($sql, $this->dblink))==FALSE)
            return($this->setError(  "MYSQL ERROR: ".mysql_error(). "   SQL: ".$sql ));

          $return = array();

          while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
            array_push ($return, $row);
  
          return $return;
    }

    public function execMyDAO($sql=null)
    {
        if (is_null($sql))
            return($this->setError("NULL query string submitted"));

        if (! $this->dblink ) 
            $this->DBConnect();

          if (($result = mysql_query($sql, $this->dblink))==FALSE)
            return($this->setError( "MYSQL ERROR: ".mysql_error(). "   SQL: ".$sql ));

        return(true);
    }

    #
    #   Error Handling
    #
    
    public function getError(){ return $this->Error; } 
    
    public function clearError(){
        $this->Error = null;
        return true;
    }
    
    protected function setError($msg=null)
    {
        if (!is_null($msg))
            $this->Error=$msg;
        else
            $this->Error="Error set to NULL";
        
        return(FALSE);  #    for convenience
    }


    #
    #   isValidId
    #
    function isValidId($id){
        $func = "get".$this->Name;
        return $this->$func($id);
    }

   
    #
    #   DAOtype - for instantiating others of the same type - set by Base
    #   

    public function getDAOType(){ return $this->DAOtype; }
    
    #
    #   Metadata
    #
    
    protected function getMetaData(){ return $this->metadata;}                            
      
    #
    #   Convenience functions
    #
        
    #
    #   lowerCaseKeyMap - Return a mapping array with the lower case key as the key, the key as the value
    #
    protected function lowerCaseKeyMap($a){
        $lca = array();
        foreach (array_keys($a) as $key){
            $lca[strtolower($key)] = $key;
        }
        return $lca;
    }
    
    #
    #   getSQLSet - return the field=value portion for a query string
    #
    protected function getSQLSet(&$obj){
        
        $meta = $this->metadata;
        $fields = array();
        $params = $obj->convertToArray(false);
        
        #   Step through the parameters
        foreach ($meta as $param => $p_a){
            if ($param != "id"){
                
                $val = $params[$param];
                
                #   Check to see if we need to quote this value
                $q = (in_array($p_a["type"],$this->quoteables)) ? "'" : "";
                
                #   If we're not quoting, check if we need to set to null
                if (($val == null||$val == "")&& !$q) $val = "NULL";
                
                #   Add it
                $fields[] = $p_a["field"]."=".$q.addslashes($val).$q;
                //$fields[] = $p_a["field"]."=".$q.$val.$q;
            }
        }
        return implode(",", $fields);
    }
    
    


}

?>
