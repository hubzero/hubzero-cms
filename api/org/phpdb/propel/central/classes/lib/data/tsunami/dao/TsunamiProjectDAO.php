<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLTsunamiProjectDAO.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/20/06
**    Copyright © 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

require_once("domain/mapper/TsunamiBaseDAO.php");
require_once("domain/tsunami/TsunamiProject.php");

class TsunamiProjectDAO extends TsunamiBaseDAO {

    protected $Name = "TsunamiProject";

    protected $metadata = array(
    

            "id" => array(
                "field" => "TsunamiProjectId",
                "type" => "int"
                ),
            "name" => array(
                "field" => "name",
                "type" => "varchar"
                ),
            "nsfTitle" => array(
                "field" => "nsfTitle",
                "type" => "varchar"
                ),
            "shortTitle" => array(
                "field" => "shortTitle",
                "type" => "varchar"
                ),
            "description" => array(
                "field" => "description",
                "type" => "text"
                ),
            "pi" => array(
                "field" => "pi",
                "type" => "varchar"
                ),
            "piInstitution" => array(
                "field" => "piInstitution",
                "type" => "varchar"
                ),
            "coPi" => array(
                "field" => "coPi",
                "type" => "varchar"
                ),
            "coPiInstitution" => array(
                "field" => "coPiInstitution",
                "type" => "varchar"
                ),
            "collaborators" => array(
                "field" => "collaborators",
                "type" => "varchar"
                ),
            "contactEmail" => array(
                "field" => "contactEmail",
                "type" => "varchar"
                ),
            "contactName" => array(
                "field" => "contactName",
                "type" => "varchar"
                ),
            "status" => array(
                "field" => "status",
                "type" => "varchar"
                ),
            "sysadminName" => array(
                "field" => "sysadminName",
                "type" => "varchar"
                ),
            "sysadminEmail" => array(
                "field" => "sysadminEmail",
                "type" => "varchar"
                ),
            "publicData" => array(
                "field" => "publicData",
                "type" => "int"
                ),
            "VIEW" => array(
                "field" => "VIEW",
                "type" => "enum"
                ),
            "DELETED" => array(
                "field" => "DELETED",
                "type" => "int"
                )    

            );
    
    #========================================================================
    #   General DAO Methods
    #========================================================================
    
    #-------------
    #   getTsunamiProject 
    #
    function getTsunamiProject($id=null){
      if (is_null($id))
         return $this->setError("id must have a value and cannot be NULL.");

        $obj = $this->newTsunamiProject();
        $obj->setId($id);
	    if($this->find($obj)) return $obj;
        else{
            $this->Error = "Could not find TsunamiProject with id of '$id'.";
            return false;
        }
    }
    
    #-------------
    #   newTsunamiProject
    #
    function newTsunamiProject(){
        $obj = new TsunamiProject();
        #   set the object's DAO to this for reference
        $obj->setDAO($this);
        return $obj;;
    }


    #-----------
    #   list
    #
    function listTsunamiProjects( &$filter = null, $expanded = false, $order = false){

        $meta = $this->metadata;
        
        if ($filter) {
            
			if($filter->setMetaData($this->metadata)==false)
				return($this->setError($filter->getError()));
			if(($str = $filter->getFilterString())==false)
				return($this->setError($filter->getError()));
			
			$where = " WHERE ".$str;
		}
		else $where = "";
        
        if(!$order) $order = "TsunamiProjectId";
        
        $sql = "SELECT * FROM TsunamiProject $where ORDER BY $order";
        
        $ret = $this->queryMyDAO($sql);
        if (!$ret) return false;
        	
        $results = array();
        
        if (count($ret) == 0) return ($results);
        
        if ($expanded){
            foreach($ret as $row){
                $results[$row["TsunamiProjectId"]] = $this->getTsunamiProject($row["TsunamiProjectId"]);
            }             
        }else{
            foreach($ret as $row){
                $results[$row["TsunamiProjectId"]] = $row["TsunamiProjectId"];
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
        $res = $this->queryMyDAO("select * from TsunamiProject where TsunamiProjectId = $objID");
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
            $this->Error = "TsunamiProject cound not be found.";
            return false;
        }
    }

	 function delete(&$obj){
		if( ($objId=$obj->getId())==-1)
			return $this->setError("Invalid ID for deletion");

		$sql = "delete from TsunamiProject where TsunamiProjectId = $objId;";
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
        $sql = "insert into TsunamiProject SET $fields;";
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
            $this->Error = "TsunamiProject id not set.";
            return false;
        }
        
        #   Check for bad id
        $checkId = $this->queryMyDAO("select TsunamiProjectId as id from TsunamiProject WHERE TsunamiProjectId = $objId;");
			if ($checkId == false)
				return false;

        if ($checkId[0]["id"] != $objId){
            $this->Error = "Bad TsunamiProject id.";
            return false;
        }
        
        #   Get fields and values
        $fields = $this->getSQLSet($obj);
        
        #   Execute the sql
        $sql = "update TsunamiProject SET $fields WHERE TsunamiProjectId = $objId;";
                
        if($this->execMyDAO($sql)==false)
				return false;
        
        return true;
    }
  
}

?>
