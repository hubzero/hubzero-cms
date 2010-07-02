<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLTsunamiSiteDAO.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/20/06
**    Copyright © 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

require_once("domain/mapper/TsunamiBaseDAO.php");
require_once("domain/tsunami/TsunamiSite.php");

class TsunamiSiteDAO extends TsunamiBaseDAO {

    protected $Name = "TsunamiSite";

    protected $metadata = array(
    

            "id" => array(
                "field" => "TsunamiSiteId",
                "type" => "int"
                ),
            "TsunamiProjectId" => array(
                "field" => "TsunamiProjectId",
                "type" => "int"
                ),
            "name" => array(
                "field" => "name",
                "type" => "varchar"
                ),
            "country" => array(
                "field" => "country",
                "type" => "varchar"
                ),
            "description" => array(
                "field" => "description",
                "type" => "text"
                ),
            "boundingPolygon" => array(
                "field" => "boundingPolygon",
                "type" => "text"
                ),
            "siteLat" => array(
                "field" => "siteLat",
                "type" => "double"
                ),
            "siteLon" => array(
                "field" => "siteLon",
                "type" => "double"
                ),
            "type" => array(
                "field" => "type",
                "type" => "enum"
                )    

            );
    
    #========================================================================
    #   General DAO Methods
    #========================================================================
    
    #-------------
    #   getTsunamiSite 
    #
    function getTsunamiSite($id=null){
      if (is_null($id))
         return $this->setError("id must have a value and cannot be NULL.");

        $obj = $this->newTsunamiSite();
        $obj->setId($id);
	    if($this->find($obj)) return $obj;
        else{
            $this->Error = "Could not find TsunamiSite with id of '$id'.";
            return false;
        }
    }
    
    #-------------
    #   newTsunamiSite
    #
    function newTsunamiSite(){
        $obj = new TsunamiSite();
        #   set the object's DAO to this for reference
        $obj->setDAO($this);
        return $obj;;
    }


    #-----------
    #   list
    #
    function listTsunamiSites( &$filter = null, $expanded = false, $order = false){

        $meta = $this->metadata;
        
        if ($filter) {
            
			if($filter->setMetaData($this->metadata)==false)
				return($this->setError($filter->getError()));
			if(($str = $filter->getFilterString())==false)
				return($this->setError($filter->getError()));
			
			$where = " WHERE ".$str;
		}
		else $where = "";
        
        if(!$order) $order = "TsunamiSiteId";
        
        $sql = "SELECT * FROM TsunamiSite $where ORDER BY $order";

        
        $ret = $this->queryMyDAO($sql);
        if (!$ret) return false;
        	
        $results = array();
        
        if (count($ret) == 0) return ($results);
        
        if ($expanded){
            foreach($ret as $row){
                $results[$row["TsunamiSiteId"]] = $this->getTsunamiSite($row["TsunamiSiteId"]);
            }             
        }else{
            foreach($ret as $row){
                $results[$row["TsunamiSiteId"]] = $row["TsunamiSiteId"];
            }
        }
        
        return $results;
    }
  
    #========================================================================
    #   Special DAO Methods
    #========================================================================


  

              
    #========================================================================
    #   Basic 4 DAO Methods
    #========================================================================
        
    #-------  
    #   Find
    #
    function find(&$obj){
        
        #   Query by id : name is not unique
        $objID = $obj->getId();
        $res = $this->queryMyDAO("select * from TsunamiSite where TsunamiSiteId = $objID");
			if ($res === false)
				return false;

        #   check for and get first result 
        if ($row = $res[0]){
            
            $meta = $this->metadata;
            $a = Array();
            
            #   Step through the metadata 
            foreach($meta as $p_name => $pa){
                if(isset($row[$pa["field"]])){
                    $a[$p_name] = $row[$pa["field"]];
                }
            }
            
            #   populate the passed object with the new array
            $obj->setFromArray($a);
            
            return true;

        }else{
            $this->Error = "TsunamiSite cound not be found.";
            return false;
        }
    }

	 function delete(&$obj){
		if( ($objId=$obj->getId())==-1)
			return $this->setError("Invalid ID for deletion");

		$sql = "delete from TsunamiSite where TsunamiSiteId = $objId;";
		if ($this->execMyDAO($sql)==false)
			return false;

		return true;
	}
    
    #------
    #   Commit
    #
    function commit(&$obj){
        
        #   Check yourself
        if( $obj->getId() != -1 ) $this->update($obj);
        
        #   Get fields and values
        $fields = $this->getSQLSet($obj);
        
        #   Execute the sql
        $sql = "insert into TsunamiSite SET $fields;";
        if($this->execMyDAO($sql)==false)
         return false;

        #   Set the new id
        $res = $this->queryMyDAO("select LAST_INSERT_ID() as id");
        $obj->setId($res[0]["id"]);
        
        return true; 
    }
    
    #------
    #   Update
    #
    function update(&$obj){ 
    
        $meta = $this->metadata;
        $objId = $obj->getId();
        
        #   Check for missing id
        if( $objId == -1 ){
            $this->Error = "TsunamiSite id not set.";
            return false;
        }
        
        #   Check for bad id
        $checkId = $this->queryMyDAO("select TsunamiSiteId as id from TsunamiSite WHERE TsunamiSiteId = $objId;");
			if ($checkId == false)
				return false;

        if ($checkId[0]["id"] != $objId){
            $this->Error = "Bad TsunamiSite id.";
            return false;
        }
        
        #   Get fields and values
        $fields = $this->getSQLSet($obj);
        
        #   Execute the sql
        $sql = "update TsunamiSite SET $fields WHERE TsunamiSiteId = $objId;";
                
        if($this->execMyDAO($sql)==false)
				return false;
        
        return true;
    }
  
}

?>
