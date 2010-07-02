<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLTsunamiBiologicalDataDAO.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/20/06
**    Copyright © 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

require_once("domain/mapper/TsunamiBaseDAO.php");
require_once("domain/tsunami/TsunamiBiologicalData.php");

class TsunamiBiologicalDataDAO extends TsunamiBaseDAO {

    protected $Name = "TsunamiBiologicalData";

    protected $metadata = array(
    

            "id" => array(
                "field" => "TsunamiBiologicalDataId",
                "type" => "int"
                ),
            "TsunamiDocLibId" => array(
                "field" => "TsunamiDocLibId",
                "type" => "int"
                ),
            "flora" => array(
                "field" => "flora",
                "type" => "int"
                ),
            "marineBiology" => array(
                "field" => "marineBiology",
                "type" => "int"
                ),
            "fauna" => array(
                "field" => "fauna",
                "type" => "int"
                )    

            );
    
    #========================================================================
    #   General DAO Methods
    #========================================================================
    
    #-------------
    #   getTsunamiBiologicalData 
    #
    function getTsunamiBiologicalData($id=null){
      if (is_null($id))
         return $this->setError("id must have a value and cannot be NULL.");

        $obj = $this->newTsunamiBiologicalData();
        $obj->setId($id);
	    if($this->find($obj)) return $obj;
        else{
            $this->Error = "Could not find TsunamiBiologicalData with id of '$id'.";
            return false;
        }
    }
    
    #-------------
    #   newTsunamiBiologicalData
    #
    function newTsunamiBiologicalData(){
        $obj = new TsunamiBiologicalData();
        #   set the object's DAO to this for reference
        $obj->setDAO($this);
        return $obj;;
    }


    #-----------
    #   list
    #
    function listTsunamiBiologicalData( &$filter = null, $expanded = false, $order = false){

        $meta = $this->metadata;
        
        if ($filter) {
            
			if($filter->setMetaData($this->metadata)==false)
				return($this->setError($filter->getError()));
			if(($str = $filter->getFilterString())==false)
				return($this->setError($filter->getError()));
			
			$where = " WHERE ".$str;
		}
		else $where = "";
        
        if(!$order) $order = "TsunamiBiologicalDataId";
        
        $sql = "SELECT * FROM TsunamiBiologicalData $where ORDER BY $order";
        
        $ret = $this->queryMyDAO($sql);
        if (!$ret) return false;
        	
        $results = array();
        
        if (count($ret) == 0) return ($results);
        
        if ($expanded){
            foreach($ret as $row){
                $results[$row["TsunamiBiologicalDataId"]] = $this->getTsunamiBiologicalData($row["TsunamiBiologicalDataId"]);
            }             
        }else{
            foreach($ret as $row){
                $results[$row["TsunamiBiologicalDataId"]] = $row["TsunamiBiologicalDataId"];
            }
        }
        
        return $results;
    }
  
    #========================================================================
    #   Special DAO Methods
    #========================================================================


    function listTsunamiBiologicalDataBySite($siteId,&$filter = null,
                                          $expanded = false, $order = false)

   {
      $meta = $this->metadata;

      if ($filter) {

         if($filter->setMetaData($this->metadata)==false)
            return($this->setError($filter->getError()));
         if(($str = $filter->getFilterString())==false)
            return($this->setError($filter->getError()));

         $where = " WHERE ".$str." AND ";
      }

      else $where = " WHERE ";

      $where.="TsunamiBiologicalData.TsunamiDocLibId = ".
              "TsunamiSiteDocRelationship.TsunamiDocLibId ".
              "AND TsunamiSiteDocRelationship.TsunamiSiteId = $siteId ";

      if(!$order) $order = "TsunamiBiologicalDataId";

      $sql = "SELECT * FROM TsunamiBiologicalData, TsunamiSiteDocRelationship $where ORDER BY $order";

      $ret = $this->queryMyDAO($sql);
      if (!$ret) return false;


      $results = array();

      if (count($ret) == 0) return ($results);


      if ($expanded){
         foreach($ret as $row) {
            $results[$row["TsunamiBiologicalDataId"]] =
               $this->getTsunamiBiologicalData($row["TsunamiBiologicalDataId"]);
         }
      }

      else {
         foreach($ret as $row) {
            $results[$row["TsunamiBiologicalDataId"]] =
               $row["TsunamiBiologicalDataId"];
         }
      }

      return $results;
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
        $res = $this->queryMyDAO("select * from TsunamiBiologicalData where TsunamiBiologicalDataId = $objID");
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
            $this->Error = "TsunamiBiologicalData cound not be found.";
            return false;
        }
    }

	 function delete(&$obj){
		if( ($objId=$obj->getId())==-1)
			return $this->setError("Invalid ID for deletion");

		$sql = "delete from TsunamiBiologicalData where TsunamiBiologicalDataId = $objId;";
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
        $sql = "insert into TsunamiBiologicalData SET $fields;";
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
            $this->Error = "TsunamiBiologicalData id not set.";
            return false;
        }
        
        #   Check for bad id
        $checkId = $this->queryMyDAO("select TsunamiBiologicalDataId as id from TsunamiBiologicalData WHERE TsunamiBiologicalDataId = $objId;");
			if ($checkId == false)
				return false;

        if ($checkId[0]["id"] != $objId){
            $this->Error = "Bad TsunamiBiologicalData id.";
            return false;
        }
        
        #   Get fields and values
        $fields = $this->getSQLSet($obj);
        
        #   Execute the sql
        $sql = "update TsunamiBiologicalData SET $fields WHERE TsunamiBiologicalDataId = $objId;";
                
        if($this->execMyDAO($sql)==false)
				return false;
        
        return true;
    }
  
}

?>
