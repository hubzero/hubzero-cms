<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLTsunamiEngineeringDataDAO.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/20/06
**    Copyright � 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

require_once("domain/mapper/TsunamiBaseDAO.php");
require_once("domain/tsunami/TsunamiEngineeringData.php");

class TsunamiEngineeringDataDAO extends TsunamiBaseDAO {

    protected $Name = "TsunamiEngineeringData";

    protected $metadata = array(


            "id" => array(
                "field" => "TsunamiEngineeringDataId",
                "type" => "int"
                ),
            "TsunamiDocLibId" => array(
                "field" => "TsunamiDocLibId",
                "type" => "int"
                ),
            "structureDamageDescr" => array(
                "field" => "structureDamageDescr",
                "type" => "int"
                ),
            "structureDesign" => array(
                "field" => "structureDesign",
                "type" => "int"
                ),
            "structureYear" => array(
                "field" => "structureYear",
                "type" => "int"
                ),
            "structureSeismicDesign" => array(
                "field" => "structureSeismicDesign",
                "type" => "int"
                ),
            "structureType" => array(
                "field" => "structureType",
                "type" => "int"
                ),
            "structureVulAssessment" => array(
                "field" => "structureVulAssessment",
                "type" => "int"
                ),
            "lifelineDesign" => array(
                "field" => "lifelineDesign",
                "type" => "int"
                ),
            "lifelineDamageDescr" => array(
                "field" => "lifelineDamageDescr",
                "type" => "int"
                ),
            "lifelineYear" => array(
                "field" => "lifelineYear",
                "type" => "int"
                ),
            "lifelineSeismicDesign" => array(
                "field" => "lifelineSeismicDesign",
                "type" => "int"
                ),
            "lifelineType" => array(
                "field" => "lifelineType",
                "type" => "int"
                ),
            "lifelineVulAssessment" => array(
                "field" => "lifelineVulAssessment",
                "type" => "int"
                ),
            "geotechSiteChar" => array(
                "field" => "geotechSiteChar",
                "type" => "int"
                ),
            "geotechSoilChar" => array(
                "field" => "geotechSoilChar",
                "type" => "int"
                ),
            "geotechDamageDescr" => array(
                "field" => "geotechDamageDescr",
                "type" => "int"
                ),
            "geotechVulAssessment" => array(
                "field" => "geotechVulAssessment",
                "type" => "int"
                ),
            "hmHazardAssessment" => array(
                "field" => "hmHazardAssessment",
                "type" => "int"
                ),
            "hmHazardMaps" => array(
                "field" => "hmHazardMaps",
                "type" => "int"
                ),
            "hmFaultMaps" => array(
                "field" => "hmFaultMaps",
                "type" => "int"
                ),
            "hmEvacPlanMaps" => array(
                "field" => "hmEvacPlanMaps",
                "type" => "int"
                ),
            "hmShelterLocations" => array(
                "field" => "hmShelterLocations",
                "type" => "int"
                ),
            "eventSensorData" => array(
                "field" => "eventSensorData",
                "type" => "int"
                ),
            "eventVideo" => array(
                "field" => "eventVideo",
                "type" => "int"
                ),
            "event" => array(
                "field" => "event",
                "type" => "int"
                ),
            "structure" => array(
                "field" => "structure",
                "type" => "int"
                ),
            "lifeline" => array(
                "field" => "lifeline",
                "type" => "int"
                ),
            "geotech" => array(
                "field" => "geotech",
                "type" => "int"
                ),
            "hm" => array(
                "field" => "hm",
                "type" => "int"
                )

            );

    #========================================================================
    #   General DAO Methods
    #========================================================================

    #-------------
    #   getTsunamiEngineeringData
    #
    function getTsunamiEngineeringData($id=null){
      if (is_null($id))
         return $this->setError("id must have a value and cannot be NULL.");

        $obj = $this->newTsunamiEngineeringData();
        $obj->setId($id);
	    if($this->find($obj)) return $obj;
        else{
            $this->Error = "Could not find TsunamiEngineeringData with id of '$id'.";
            return false;
        }
    }

    #-------------
    #   newTsunamiEngineeringData
    #
    function newTsunamiEngineeringData(){
        $obj = new TsunamiEngineeringData();
        #   set the object's DAO to this for reference
        $obj->setDAO($this);
        return $obj;;
    }


    #-----------
    #   list
    #
    function listTsunamiEngineeringData( &$filter = null, $expanded = false, $order = false){

        $meta = $this->metadata;

        if ($filter) {

			if($filter->setMetaData($this->metadata)==false)
				return($this->setError($filter->getError()));
			if(($str = $filter->getFilterString())==false)
				return($this->setError($filter->getError()));

			$where = " WHERE ".$str;
		}
		else $where = "";

        if(!$order) $order = "TsunamiEngineeringDataId";

        $sql = "SELECT * FROM TsunamiEngineeringData $where ORDER BY $order";

        $ret = $this->queryMyDAO($sql);
        if (!$ret) return false;

        $results = array();

        if (count($ret) == 0) return ($results);

        if ($expanded){
            foreach($ret as $row){
                $results[$row["TsunamiEngineeringDataId"]] = $this->getTsunamiEngineeringData($row["TsunamiEngineeringDataId"]);
            }
        }else{
            foreach($ret as $row){
                $results[$row["TsunamiEngineeringDataId"]] = $row["TsunamiEngineeringDataId"];
            }
        }

        return $results;
    }

    #========================================================================
    #   Special DAO Methods
    #========================================================================


    function listTsunamiEngineeringDataBySite($siteId,&$filter = null,
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

      $where.="TsunamiEngineeringData.TsunamiDocLibId = ".
              "TsunamiSiteDocRelationship.TsunamiDocLibId ".
              "AND TsunamiSiteDocRelationship.TsunamiSiteId = $siteId ";

      if(!$order) $order = "TsunamiEngineeringDataId";

      $sql = "SELECT * FROM TsunamiEngineeringData, TsunamiSiteDocRelationship $where ORDER BY $order";

      $ret = $this->queryMyDAO($sql);
      if (!$ret) return false;


      $results = array();

      if (count($ret) == 0) return ($results);


      if ($expanded){
         foreach($ret as $row) {
            $results[$row["TsunamiEngineeringDataId"]] =
               $this->getTsunamiEngineeringData($row["TsunamiEngineeringDataId"]);
         }
      }

      else {
         foreach($ret as $row) {
            $results[$row["TsunamiEngineeringDataId"]] =
               $row["TsunamiEngineeringDataId"];
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
        $res = $this->queryMyDAO("select * from TsunamiEngineeringData where TsunamiEngineeringDataId = $objID");
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
            $this->Error = "TsunamiEngineeringData cound not be found.";
            return false;
        }
    }

	 function delete(&$obj){
		if( ($objId=$obj->getId())==-1)
			return $this->setError("Invalid ID for deletion");

		$sql = "delete from TsunamiEngineeringData where TsunamiEngineeringDataId = $objId;";
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
        $sql = "insert into TsunamiEngineeringData SET $fields;";
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
            $this->Error = "TsunamiEngineeringData id not set.";
            return false;
        }

        #   Check for bad id
        $checkId = $this->queryMyDAO("select TsunamiEngineeringDataId as id from TsunamiEngineeringData WHERE TsunamiEngineeringDataId = $objId;");
			if ($checkId == false)
				return false;

        if ($checkId[0]["id"] != $objId){
            $this->Error = "Bad TsunamiEngineeringData id.";
            return false;
        }

        #   Get fields and values
        $fields = $this->getSQLSet($obj);

        #   Execute the sql
        $sql = "update TsunamiEngineeringData SET $fields WHERE TsunamiEngineeringDataId = $objId;";

        if($this->execMyDAO($sql)==false)
				return false;

        return true;
    }

}

?>
