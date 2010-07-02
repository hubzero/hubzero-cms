<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLTsunamiHydrodynamicDataDAO.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/20/06
**    Copyright © 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

require_once("domain/mapper/TsunamiBaseDAO.php");
require_once("domain/tsunami/TsunamiHydrodynamicData.php");

class TsunamiHydrodynamicDataDAO extends TsunamiBaseDAO {

    protected $Name = "TsunamiHydrodynamicData";

    protected $metadata = array(
    

            "id" => array(
                "field" => "TsunamiHydrodynamicDataId",
                "type" => "int"
                ),
            "TsunamiDocLibId" => array(
                "field" => "TsunamiDocLibId",
                "type" => "int"
                ),
            "runupSource" => array(
                "field" => "runupSource",
                "type" => "int"
                ),
            "runupHeight" => array(
                "field" => "runupHeight",
                "type" => "int"
                ),
            "runupPoRLoc" => array(
                "field" => "runupPoRLoc",
                "type" => "int"
                ),
            "runupPoRHeight" => array(
                "field" => "runupPoRHeight",
                "type" => "int"
                ),
            "runupTidalAdj" => array(
                "field" => "runupTidalAdj",
                "type" => "int"
                ),
            "runupAdjMethod" => array(
                "field" => "runupAdjMethod",
                "type" => "int"
                ),
            "runupQuality" => array(
                "field" => "runupQuality",
                "type" => "int"
                ),
            "inundationSource" => array(
                "field" => "inundationSource",
                "type" => "int"
                ),
            "inundationDist" => array(
                "field" => "inundationDist",
                "type" => "int"
                ),
            "inundationQuality" => array(
                "field" => "inundationQuality",
                "type" => "int"
                ),
            "tidegaugeSource" => array(
                "field" => "tidegaugeSource",
                "type" => "int"
                ),
            "tidegaugeType" => array(
                "field" => "tidegaugeType",
                "type" => "int"
                ),
            "flowDirection" => array(
                "field" => "flowDirection",
                "type" => "int"
                ),
            "flowSpeed" => array(
                "field" => "flowSpeed",
                "type" => "int"
                ),
            "flowSource" => array(
                "field" => "flowSource",
                "type" => "int"
                ),
            "waveNumber" => array(
                "field" => "waveNumber",
                "type" => "int"
                ),
            "waveArrivalTimes" => array(
                "field" => "waveArrivalTimes",
                "type" => "int"
                ),
            "waveForm" => array(
                "field" => "waveForm",
                "type" => "int"
                ),
            "waveHeight" => array(
                "field" => "waveHeight",
                "type" => "int"
                ),
            "wavePeriod" => array(
                "field" => "wavePeriod",
                "type" => "int"
                ),
            "waveTimeToNorm" => array(
                "field" => "waveTimeToNorm",
                "type" => "int"
                ),
            "waveSource" => array(
                "field" => "waveSource",
                "type" => "int"
                ),
            "conditionWind" => array(
                "field" => "conditionWind",
                "type" => "int"
                ),
            "conditionWeather" => array(
                "field" => "conditionWeather",
                "type" => "int"
                ),
            "conditionSea" => array(
                "field" => "conditionSea",
                "type" => "int"
                ),
            "conditionSource" => array(
                "field" => "conditionSource",
                "type" => "int"
                ),
            "runup" => array(
                "field" => "runup",
                "type" => "int"
                ),
            "inundation" => array(
                "field" => "inundation",
                "type" => "int"
                ),
            "tidegauge" => array(
                "field" => "tidegauge",
                "type" => "int"
                ),
            "flow" => array(
                "field" => "flow",
                "type" => "int"
                ),
            "wave" => array(
                "field" => "wave",
                "type" => "int"
                ),
            "econdition" => array(
                "field" => "econdition",
                "type" => "int"
                )    

            );
    
    #========================================================================
    #   General DAO Methods
    #========================================================================
    
    #-------------
    #   getTsunamiHydrodynamicData 
    #
    function getTsunamiHydrodynamicData($id=null){
      if (is_null($id))
         return $this->setError("id must have a value and cannot be NULL.");

        $obj = $this->newTsunamiHydrodynamicData();
        $obj->setId($id);
	    if($this->find($obj)) return $obj;
        else{
            $this->Error = "Could not find TsunamiHydrodynamicData with id of '$id'.";
            return false;
        }
    }
    
    #-------------
    #   newTsunamiHydrodynamicData
    #
    function newTsunamiHydrodynamicData(){
        $obj = new TsunamiHydrodynamicData();
        #   set the object's DAO to this for reference
        $obj->setDAO($this);
        return $obj;;
    }


    #-----------
    #   list
    #
    function listTsunamiHydrodynamicData( &$filter = null, $expanded = false, $order = false){

        $meta = $this->metadata;
        
        if ($filter) {
            
			if($filter->setMetaData($this->metadata)==false)
				return($this->setError($filter->getError()));
			if(($str = $filter->getFilterString())==false)
				return($this->setError($filter->getError()));
			
			$where = " WHERE ".$str;
		}
		else $where = "";
        
        if(!$order) $order = "TsunamiHydrodynamicDataId";
        
        $sql = "SELECT * FROM TsunamiHydrodynamicData $where ORDER BY $order";
        
        $ret = $this->queryMyDAO($sql);
        if (!$ret) return false;
        	
        $results = array();
        
        if (count($ret) == 0) return ($results);
        
        if ($expanded){
            foreach($ret as $row){
                $results[$row["TsunamiHydrodynamicDataId"]] = $this->getTsunamiHydrodynamicData($row["TsunamiHydrodynamicDataId"]);
            }             
        }else{
            foreach($ret as $row){
                $results[$row["TsunamiHydrodynamicDataId"]] = $row["TsunamiHydrodynamicDataId"];
            }
        }
        
        return $results;
    }
  
    #========================================================================
    #   Special DAO Methods
    #========================================================================


    function listTsunamiHydrodynamicDataBySite($siteId,&$filter = null,
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

      $where.="TsunamiHydrodynamicData.TsunamiDocLibId = ".
              "TsunamiSiteDocRelationship.TsunamiDocLibId ".
              "AND TsunamiSiteDocRelationship.TsunamiSiteId = $siteId ";

      if(!$order) $order = "TsunamiHydrodynamicDataId";

      $sql = "SELECT * FROM TsunamiHydrodynamicData, TsunamiSiteDocRelationship $where ORDER BY $order";

      $ret = $this->queryMyDAO($sql);
      if (!$ret) return false;


      $results = array();

      if (count($ret) == 0) return ($results);


      if ($expanded){
         foreach($ret as $row) {
            $results[$row["TsunamiHydrodynamicDataId"]] =
               $this->getTsunamiHydrodynamicData($row["TsunamiHydrodynamicDataId"]);
         }
      }

      else {
         foreach($ret as $row) {
            $results[$row["TsunamiHydrodynamicDataId"]] =
               $row["TsunamiHydrodynamicDataId"];
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
        $res = $this->queryMyDAO("select * from TsunamiHydrodynamicData where TsunamiHydrodynamicDataId = $objID");
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
            $this->Error = "TsunamiHydrodynamicData cound not be found.";
            return false;
        }
    }

	 function delete(&$obj){
		if( ($objId=$obj->getId())==-1)
			return $this->setError("Invalid ID for deletion");

		$sql = "delete from TsunamiHydrodynamicData where TsunamiHydrodynamicDataId = $objId;";
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
        $sql = "insert into TsunamiHydrodynamicData SET $fields;";
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
            $this->Error = "TsunamiHydrodynamicData id not set.";
            return false;
        }
        
        #   Check for bad id
        $checkId = $this->queryMyDAO("select TsunamiHydrodynamicDataId as id from TsunamiHydrodynamicData WHERE TsunamiHydrodynamicDataId = $objId;");
			if ($checkId == false)
				return false;

        if ($checkId[0]["id"] != $objId){
            $this->Error = "Bad TsunamiHydrodynamicData id.";
            return false;
        }
        
        #   Get fields and values
        $fields = $this->getSQLSet($obj);
        
        #   Execute the sql
        $sql = "update TsunamiHydrodynamicData SET $fields WHERE TsunamiHydrodynamicDataId = $objId;";
                
        if($this->execMyDAO($sql)==false)
				return false;
        
        return true;
    }
  
}

?>
