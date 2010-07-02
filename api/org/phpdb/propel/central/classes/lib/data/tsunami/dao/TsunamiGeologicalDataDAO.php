<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLTsunamiGeologicalDataDAO.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/20/06
**    Copyright © 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

require_once("domain/mapper/TsunamiBaseDAO.php");
require_once("domain/tsunami/TsunamiGeologicalData.php");

class TsunamiGeologicalDataDAO extends TsunamiBaseDAO {

    protected $Name = "TsunamiGeologicalData";

    protected $metadata = array(
    

            "id" => array(
                "field" => "TsunamiGeologicalDataId",
                "type" => "int"
                ),
            "TsunamiDocLibId" => array(
                "field" => "TsunamiDocLibId",
                "type" => "int"
                ),
            "faultType" => array(
                "field" => "faultType",
                "type" => "int"
                ),
            "faultStrikeMeasure" => array(
                "field" => "faultStrikeMeasure",
                "type" => "int"
                ),
            "faultOffset" => array(
                "field" => "faultOffset",
                "type" => "int"
                ),
            "faultGeomorphic" => array(
                "field" => "faultGeomorphic",
                "type" => "int"
                ),
            "faultPaleo" => array(
                "field" => "faultPaleo",
                "type" => "int"
                ),
            "displacementUplift" => array(
                "field" => "displacementUplift",
                "type" => "int"
                ),
            "displacementSubsidence" => array(
                "field" => "displacementSubsidence",
                "type" => "int"
                ),
            "tdcbmElevation" => array(
                "field" => "tdcbmElevation",
                "type" => "int"
                ),
            "tdcbmDistInland" => array(
                "field" => "tdcbmDistInland",
                "type" => "int"
                ),
            "tdcbmScale" => array(
                "field" => "tdcbmScale",
                "type" => "int"
                ),
            "tdcbmSpatialVar" => array(
                "field" => "tdcbmSpatialVar",
                "type" => "int"
                ),
            "tdcbmCharacteristics" => array(
                "field" => "tdcbmCharacteristics",
                "type" => "int"
                ),
            "gmchangesScour" => array(
                "field" => "gmchangesScour",
                "type" => "int"
                ),
            "gmchangesDeposit" => array(
                "field" => "gmchangesDeposit",
                "type" => "int"
                ),
            "gmchangesBedMod" => array(
                "field" => "gmchangesBedMod",
                "type" => "int"
                ),
            "eilElevation" => array(
                "field" => "eilElevation",
                "type" => "int"
                ),
            "eilDistInland" => array(
                "field" => "eilDistInland",
                "type" => "int"
                ),
            "eilCharacteristics" => array(
                "field" => "eilCharacteristics",
                "type" => "int"
                ),
            "sslScars" => array(
                "field" => "sslScars",
                "type" => "int"
                ),
            "sslDeposits" => array(
                "field" => "sslDeposits",
                "type" => "int"
                ),
            "sslCoefficientOfFriction" => array(
                "field" => "sslCoefficientOfFriction",
                "type" => "int"
                ),
            "paleoElevation" => array(
                "field" => "paleoElevation",
                "type" => "int"
                ),
            "paleoDistInland" => array(
                "field" => "paleoDistInland",
                "type" => "int"
                ),
            "paleoScale" => array(
                "field" => "paleoScale",
                "type" => "int"
                ),
            "paleoSpatialVar" => array(
                "field" => "paleoSpatialVar",
                "type" => "int"
                ),
            "paleoCharacteristics" => array(
                "field" => "paleoCharacteristics",
                "type" => "int"
                ),
            "paleoOutcrops" => array(
                "field" => "paleoOutcrops",
                "type" => "int"
                ),
            "paleoSedPeels" => array(
                "field" => "paleoSedPeels",
                "type" => "int"
                ),
            "paleoCoreSamples" => array(
                "field" => "paleoCoreSamples",
                "type" => "int"
                ),
            "fault" => array(
                "field" => "fault",
                "type" => "int"
                ),
            "displacement" => array(
                "field" => "displacement",
                "type" => "int"
                ),
            "tdcbm" => array(
                "field" => "tdcbm",
                "type" => "int"
                ),
            "gmchanges" => array(
                "field" => "gmchanges",
                "type" => "int"
                ),
            "eil" => array(
                "field" => "eil",
                "type" => "int"
                ),
            "smsl" => array(
                "field" => "smsl",
                "type" => "int"
                ),
            "paleo" => array(
                "field" => "paleo",
                "type" => "int"
                )    

            );
    
    #========================================================================
    #   General DAO Methods
    #========================================================================
    
    #-------------
    #   getTsunamiGeologicalData 
    #
    function getTsunamiGeologicalData($id=null){
      if (is_null($id))
         return $this->setError("id must have a value and cannot be NULL.");

        $obj = $this->newTsunamiGeologicalData();
        $obj->setId($id);
	    if($this->find($obj)) return $obj;
        else{
            $this->Error = "Could not find TsunamiGeologicalData with id of '$id'.";
            return false;
        }
    }
    
    #-------------
    #   newTsunamiGeologicalData
    #
    function newTsunamiGeologicalData(){
        $obj = new TsunamiGeologicalData();
        #   set the object's DAO to this for reference
        $obj->setDAO($this);
        return $obj;;
    }


    #-----------
    #   list
    #
    function listTsunamiGeologicalData( &$filter = null, $expanded = false, $order = false){

        $meta = $this->metadata;
        
        if ($filter) {
            
			if($filter->setMetaData($this->metadata)==false)
				return($this->setError($filter->getError()));
			if(($str = $filter->getFilterString())==false)
				return($this->setError($filter->getError()));
			
			$where = " WHERE ".$str;
		}
		else $where = "";
        
        if(!$order) $order = "TsunamiGeologicalDataId";
        
        $sql = "SELECT * FROM TsunamiGeologicalData $where ORDER BY $order";
        
        $ret = $this->queryMyDAO($sql);
        if (!$ret) return false;
        	
        $results = array();
        
        if (count($ret) == 0) return ($results);
        
        if ($expanded){
            foreach($ret as $row){
                $results[$row["TsunamiGeologicalDataId"]] = $this->getTsunamiGeologicalData($row["TsunamiGeologicalDataId"]);
            }             
        }else{
            foreach($ret as $row){
                $results[$row["TsunamiGeologicalDataId"]] = $row["TsunamiGeologicalDataId"];
            }
        }
        
        return $results;
    }
  
    #========================================================================
    #   Special DAO Methods
    #========================================================================


    function listTsunamiGeologicalDataBySite($siteId,&$filter = null,
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

      $where.="TsunamiGeologicalData.TsunamiDocLibId = ".
              "TsunamiSiteDocRelationship.TsunamiDocLibId ".
              "AND TsunamiSiteDocRelationship.TsunamiSiteId = $siteId ";

      if(!$order) $order = "TsunamiGeologicalDataId";

      $sql = "SELECT * FROM TsunamiGeologicalData, TsunamiSiteDocRelationship $where ORDER BY $order";

      $ret = $this->queryMyDAO($sql);
      if (!$ret) return false;


      $results = array();

      if (count($ret) == 0) return ($results);


      if ($expanded){
         foreach($ret as $row) {
            $results[$row["TsunamiGeologicalDataId"]] =
               $this->getTsunamiGeologicalData($row["TsunamiGeologicalDataId"]);
         }
      }

      else {
         foreach($ret as $row) {
            $results[$row["TsunamiGeologicalDataId"]] =
               $row["TsunamiGeologicalDataId"];
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
        $res = $this->queryMyDAO("select * from TsunamiGeologicalData where TsunamiGeologicalDataId = $objID");
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
            $this->Error = "TsunamiGeologicalData cound not be found.";
            return false;
        }
    }

	 function delete(&$obj){
		if( ($objId=$obj->getId())==-1)
			return $this->setError("Invalid ID for deletion");

		$sql = "delete from TsunamiGeologicalData where TsunamiGeologicalDataId = $objId;";
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
        $sql = "insert into TsunamiGeologicalData SET $fields;";
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
            $this->Error = "TsunamiGeologicalData id not set.";
            return false;
        }
        
        #   Check for bad id
        $checkId = $this->queryMyDAO("select TsunamiGeologicalDataId as id from TsunamiGeologicalData WHERE TsunamiGeologicalDataId = $objId;");
			if ($checkId == false)
				return false;

        if ($checkId[0]["id"] != $objId){
            $this->Error = "Bad TsunamiGeologicalData id.";
            return false;
        }
        
        #   Get fields and values
        $fields = $this->getSQLSet($obj);
        
        #   Execute the sql
        $sql = "update TsunamiGeologicalData SET $fields WHERE TsunamiGeologicalDataId = $objId;";
                
        if($this->execMyDAO($sql)==false)
				return false;
        
        return true;
    }
  
}

?>
