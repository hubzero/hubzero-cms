<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLTsunamiSeismicDataDAO.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/20/06
**    Copyright © 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

require_once("domain/mapper/TsunamiBaseDAO.php");
require_once("domain/tsunami/TsunamiSeismicData.php");

class TsunamiSeismicDataDAO extends TsunamiBaseDAO {

    protected $Name = "TsunamiSeismicData";

    protected $metadata = array(
    

            "id" => array(
                "field" => "TsunamiSeismicDataId",
                "type" => "int"
                ),
            "TsunamiDocLibId" => array(
                "field" => "TsunamiDocLibId",
                "type" => "int"
                ),
            "localType" => array(
                "field" => "localType",
                "type" => "int"
                ),
            "localDataSources" => array(
                "field" => "localDataSources",
                "type" => "int"
                ),
            "miaSource" => array(
                "field" => "miaSource",
                "type" => "int"
                ),
            "measuresTypes" => array(
                "field" => "measuresTypes",
                "type" => "int"
                ),
            "measuresSiteConfig" => array(
                "field" => "measuresSiteConfig",
                "type" => "int"
                ),
            "local" => array(
                "field" => "local",
                "type" => "int"
                ),
            "mia" => array(
                "field" => "mia",
                "type" => "int"
                ),
            "measures" => array(
                "field" => "measures",
                "type" => "int"
                )    

            );
    
    #========================================================================
    #   General DAO Methods
    #========================================================================
    
    #-------------
    #   getTsunamiSeismicData 
    #
    function getTsunamiSeismicData($id=null){
      if (is_null($id))
         return $this->setError("id must have a value and cannot be NULL.");

        $obj = $this->newTsunamiSeismicData();
        $obj->setId($id);
	    if($this->find($obj)) return $obj;
        else{
            $this->Error = "Could not find TsunamiSeismicData with id of '$id'.";
            return false;
        }
    }
    
    #-------------
    #   newTsunamiSeismicData
    #
    function newTsunamiSeismicData(){
        $obj = new TsunamiSeismicData();
        #   set the object's DAO to this for reference
        $obj->setDAO($this);
        return $obj;;
    }


    #-----------
    #   list
    #
    function listTsunamiSeismicData( &$filter = null, $expanded = false, $order = false){

        $meta = $this->metadata;
        
        if ($filter) {
            
			if($filter->setMetaData($this->metadata)==false)
				return($this->setError($filter->getError()));
			if(($str = $filter->getFilterString())==false)
				return($this->setError($filter->getError()));
			
			$where = " WHERE ".$str;
		}
		else $where = "";
        
        if(!$order) $order = "TsunamiSeismicDataId";
        
        $sql = "SELECT * FROM TsunamiSeismicData $where ORDER BY $order";
        
        
        $ret = $this->queryMyDAO($sql);
        if (!$ret) return false;
        	
        $results = array();
        
        if (count($ret) == 0) return ($results);
        
        if ($expanded){
            foreach($ret as $row){
                $results[$row["TsunamiSeismicDataId"]] = $this->getTsunamiSeismicData($row["TsunamiSeismicDataId"]);
            }             
        }else{
            foreach($ret as $row){
                $results[$row["TsunamiSeismicDataId"]] = $row["TsunamiSeismicDataId"];
            }
        }
        
        return $results;
    }
  
    #========================================================================
    #   Special DAO Methods
    #========================================================================


    function listTsunamiSeismicDataBySite($siteId,&$filter = null,
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

      $where.="TsunamiSeismicData.TsunamiDocLibId = ".
              "TsunamiSiteDocRelationship.TsunamiDocLibId ".
              "AND TsunamiSiteDocRelationship.TsunamiSiteId = $siteId ";

      if(!$order) $order = "TsunamiSeismicDataId";

      $sql = "SELECT * FROM TsunamiSeismicData, TsunamiSiteDocRelationship $where ORDER BY $order";

      $ret = $this->queryMyDAO($sql);
      if (!$ret) return false;


      $results = array();

      if (count($ret) == 0) return ($results);


      if ($expanded){
         foreach($ret as $row) {
            $results[$row["TsunamiSeismicDataId"]] =
               $this->getTsunamiSeismicData($row["TsunamiSeismicDataId"]);
         }
      }

      else {
         foreach($ret as $row) {
            $results[$row["TsunamiSeismicDataId"]] =
               $row["TsunamiSeismicDataId"];
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
        $res = $this->queryMyDAO("select * from TsunamiSeismicData where TsunamiSeismicDataId = $objID");
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
            $this->Error = "TsunamiSeismicData cound not be found.";
            return false;
        }
    }

	 function delete(&$obj){
		if( ($objId=$obj->getId())==-1)
			return $this->setError("Invalid ID for deletion");

		$sql = "delete from TsunamiSeismicData where TsunamiSeismicDataId = $objId;";
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
        $sql = "insert into TsunamiSeismicData SET $fields;";
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
            $this->Error = "TsunamiSeismicData id not set.";
            return false;
        }
        
        #   Check for bad id
        $checkId = $this->queryMyDAO("select TsunamiSeismicDataId as id from TsunamiSeismicData WHERE TsunamiSeismicDataId = $objId;");
			if ($checkId == false)
				return false;

        if ($checkId[0]["id"] != $objId){
            $this->Error = "Bad TsunamiSeismicData id.";
            return false;
        }
        
        #   Get fields and values
        $fields = $this->getSQLSet($obj);
        
        #   Execute the sql
        $sql = "update TsunamiSeismicData SET $fields WHERE TsunamiSeismicDataId = $objId;";
                
        if($this->execMyDAO($sql)==false)
				return false;
        
        return true;
    }
  
}

?>
