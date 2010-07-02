<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLTsunamiDocLibDAO.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/20/06
**    Copyright © 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

require_once("domain/mapper/TsunamiBaseDAO.php");
require_once("domain/tsunami/TsunamiDocLib.php");

class TsunamiDocLibDAO extends TsunamiBaseDAO {

    protected $Name = "TsunamiDocLib";

    protected $metadata = array(
    

            "id" => array(
                "field" => "TsunamiDocLibId",
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
            "title" => array(
                "field" => "title",
                "type" => "varchar"
                ),
            "description" => array(
                "field" => "description",
                "type" => "text"
                ),
            "authors" => array(
                "field" => "authors",
                "type" => "varchar"
                ),
            "authorEmails" => array(
                "field" => "authorEmails",
                "type" => "varchar"
                ),
            "date" => array(
                "field" => "date",
                "type" => "date"
                ),
            "howToCite" => array(
                "field" => "howToCite",
                "type" => "varchar"
                ),
            "specificLat" => array(
                "field" => "specificLat",
                "type" => "double"
                ),
            "specificLon" => array(
                "field" => "specificLon",
                "type" => "double"
                ),
            "typeOfMaterial" => array(
                "field" => "typeOfMaterial",
                "type" => "enum"
                ),
            "fileLocation" => array(
                "field" => "fileLocation",
                "type" => "varchar"
                ),
            "fileSize" => array(
                "field" => "fileSize",
                "type" => "int"
                ),
            "dirty" => array(
                "field" => "dirty",
                "type" => "int"
                )    

            );
    
    #========================================================================
    #   General DAO Methods
    #========================================================================
    
    #-------------
    #   getTsunamiDocLib 
    #
    function getTsunamiDocLib($id=null){
      if (is_null($id))
         return $this->setError("id must have a value and cannot be NULL.");

        $obj = $this->newTsunamiDocLib();
        $obj->setId($id);
	    if($this->find($obj)) return $obj;
        else{
            $this->Error = "Could not find TsunamiDocLib with id of '$id'.";
            return false;
        }
    }

    function getTsunamiDocSiteId($id=null) {
      if (is_null($id))
        return $this->setError("id must have a value and cannot be NULL.");

      $sql = "SELECT TsunamiSiteId from TsunamiSiteDocRelationship where $id = TsunamiDocLibId";
      $ret = $this->queryMyDAO($sql);
      if (!$ret)
        return $this->setError("getTsubanuDocSite could not find TsunamiDocLib with id of '$id'.");
      if (count($ret) == 0)
        return $this->setError("getTsubanuDocSite could not find TsunamiDocLib with id of '$id'.");

      $results = $ret[0]["TsunamiSiteId"];

      return ($results);
    }
        
    
    #-------------
    #   newTsunamiDocLib
    #
    function newTsunamiDocLib(){
        $obj = new TsunamiDocLib();
        #   set the object's DAO to this for reference
        $obj->setDAO($this);
        return $obj;;
    }


    #-----------
    #   list
    #
    function listTsunamiDocLibs( &$filter = null, $expanded = false, $order = false){

        $meta = $this->metadata;
        
        if ($filter) {
            
			if($filter->setMetaData($this->metadata)==false)
				return($this->setError($filter->getError()));
			if(($str = $filter->getFilterString())==false)
				return($this->setError($filter->getError()));
			
			$where = " WHERE ".$str;
		}
		else $where = "";
        
        if(!$order) $order = "TsunamiDocLibId";
        
        $sql = "SELECT * FROM TsunamiDocLib $where ORDER BY $order";
        
        $ret = $this->queryMyDAO($sql);
        if (!$ret) return false;
        	
        $results = array();
        
        if (count($ret) == 0) return ($results);
        
        if ($expanded){
            foreach($ret as $row){
                $results[$row["TsunamiDocLibId"]] = $this->getTsunamiDocLib($row["TsunamiDocLibId"]);
            }             
        }else{
            foreach($ret as $row){
                $results[$row["TsunamiDocLibId"]] = $row["TsunamiDocLibId"];
            }
        }
        
        return $results;
    }
  
    #========================================================================
    #   Special DAO Methods
    #========================================================================


 	function linkDocToSite($dObj=null,$sId=null)
	{
		//Check dObj for null

		//check sId for null

		$dId = $dObj->getId();

		$sql = 	"INSERT INTO `TsunamiSiteDocRelationship`".
					" ( `TsunamiSiteId` , `TsunamiDocLibId` )".
					" VALUES ( '".$sId."', '".$dId."')";

		$res=$this->execMyDAO($sql);

		if ($res == false)
			return false;

		return true;
}
	
 

              
    #========================================================================
    #   Basic 4 DAO Methods
    #========================================================================
        
    #-------  
    #   Find
    #
    function find(&$obj){
        
        #   Query by id : name is not unique
        $objID = $obj->getId();
        $res = $this->queryMyDAO("select * from TsunamiDocLib where TsunamiDocLibId = $objID");
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
            $this->Error = "TsunamiDocLib cound not be found.";
            return false;
        }
    }

  function deleteSiteDoc(&$obj){
    if( ($objId=$obj->getId())==-1)
      return $this->setError("Invalid ID for deletion");

    $sql = "delete from TsunamiSiteDocRelationship where TsunamiDocLibId = $objId;";
		if ($this->execMyDAO($sql)==false)
			return false;

		return true;
	}

	 function delete(&$obj){
		if( ($objId=$obj->getId())==-1)
			return $this->setError("Invalid ID for deletion");

		$sql = "delete from TsunamiDocLib where TsunamiDocLibId = $objId;";
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
        $sql = "insert into TsunamiDocLib SET $fields;";
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
            $this->Error = "TsunamiDocLib id not set.";
            return false;
        }
        
        #   Check for bad id
        $checkId = $this->queryMyDAO("select TsunamiDocLibId as id from TsunamiDocLib WHERE TsunamiDocLibId = $objId;");
			if ($checkId == false)
				return false;

        if ($checkId[0]["id"] != $objId){
            $this->Error = "Bad TsunamiDocLib id.";
            return false;
        }
        
        #   Get fields and values
        $fields = $this->getSQLSet($obj);
        
        #   Execute the sql
        $sql = "update TsunamiDocLib SET $fields WHERE TsunamiDocLibId = $objId;";
                
        if($this->execMyDAO($sql)==false)
				return false;
        
        return true;
    }
  
}

?>
