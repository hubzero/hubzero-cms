<?php
/********************************************************************
**
** The code written by:
**
**    NACSE Team, Febuary 2006, Adam Ryan, Ben Steinberg
**    Module Name: MySQLTsunamiSocialScienceDataDAO.php
**    Last Modification by: Adam Ryan
**    Last Modified date: 2/20/06
**    Copyright © 2006
**       Northwest Alliance for Computational Science and Engineering
**       (NACSE, www.nacse.org)
**
**
********************************************************************/

require_once("domain/mapper/TsunamiBaseDAO.php");
require_once("domain/tsunami/TsunamiSocialScienceData.php");

class TsunamiSocialScienceDataDAO extends TsunamiBaseDAO {

    protected $Name = "TsunamiSocialScienceData";

    protected $metadata = array(
    

            "id" => array(
                "field" => "TsunamiSocialScienceDataId",
                "type" => "int"
                ),
            "TsunamiDocLibId" => array(
                "field" => "TsunamiDocLibId",
                "type" => "int"
                ),
            "impactNumDead" => array(
                "field" => "impactNumDead",
                "type" => "int"
                ),
            "impactNumInjured" => array(
                "field" => "impactNumInjured",
                "type" => "int"
                ),
            "impactNumMissing" => array(
                "field" => "impactNumMissing",
                "type" => "int"
                ),
            "impactNumHomeless" => array(
                "field" => "impactNumHomeless",
                "type" => "int"
                ),
            "impactNumFamSep" => array(
                "field" => "impactNumFamSep",
                "type" => "int"
                ),
            "iresponsePrep" => array(
                "field" => "iresponsePrep",
                "type" => "int"
                ),
            "iresponseWarnings" => array(
                "field" => "iresponseWarnings",
                "type" => "int"
                ),
            "iresponseRecovery" => array(
                "field" => "iresponseRecovery",
                "type" => "int"
                ),
            "iresponseMitigation" => array(
                "field" => "iresponseMitigation",
                "type" => "int"
                ),
            "iresponseIntervw" => array(
                "field" => "iresponseIntervw",
                "type" => "int"
                ),
            "cresponsePrep" => array(
                "field" => "cresponsePrep",
                "type" => "int"
                ),
            "cresponseWarning" => array(
                "field" => "cresponseWarning",
                "type" => "int"
                ),
            "cresponseRecovery" => array(
                "field" => "cresponseRecovery",
                "type" => "int"
                ),
            "cresponseMitigation" => array(
                "field" => "cresponseMitigation",
                "type" => "int"
                ),
            "cresponseIntervw" => array(
                "field" => "cresponseIntervw",
                "type" => "int"
                ),
            "oresponsePrep" => array(
                "field" => "oresponsePrep",
                "type" => "int"
                ),
            "oresponseRecovery" => array(
                "field" => "oresponseRecovery",
                "type" => "int"
                ),
            "oresponseMitigation" => array(
                "field" => "oresponseMitigation",
                "type" => "int"
                ),
            "oresponseDisease" => array(
                "field" => "oresponseDisease",
                "type" => "int"
                ),
            "oresponseNGORelief" => array(
                "field" => "oresponseNGORelief",
                "type" => "int"
                ),
            "oresponseGrelief" => array(
                "field" => "oresponseGrelief",
                "type" => "int"
                ),
            "oresponseIntervw" => array(
                "field" => "oresponseIntervw",
                "type" => "int"
                ),
            "commWarnSys" => array(
                "field" => "commWarnSys",
                "type" => "int"
                ),
            "commInfoFromG" => array(
                "field" => "commInfoFromG",
                "type" => "int"
                ),
            "damageCostEst" => array(
                "field" => "damageCostEst",
                "type" => "int"
                ),
            "damageIndustry" => array(
                "field" => "damageIndustry",
                "type" => "int"
                ),
            "damageType" => array(
                "field" => "damageType",
                "type" => "int"
                ),
            "bkgCensus" => array(
                "field" => "bkgCensus",
                "type" => "int"
                ),
            "bkgTransportSystems" => array(
                "field" => "bkgTransportSystems",
                "type" => "int"
                ),
            "bkgTouristStats" => array(
                "field" => "bkgTouristStats",
                "type" => "int"
                ),
            "bkgLanguageIssues" => array(
                "field" => "bkgLanguageIssues",
                "type" => "int"
                ),
            "bkg" => array(
                "field" => "bkg",
                "type" => "int"
                ),
            "impact" => array(
                "field" => "impact",
                "type" => "int"
                ),
            "comm" => array(
                "field" => "comm",
                "type" => "int"
                ),
            "iresponse" => array(
                "field" => "iresponse",
                "type" => "int"
                ),
            "cresponse" => array(
                "field" => "cresponse",
                "type" => "int"
                ),
            "oresponse" => array(
                "field" => "oresponse",
                "type" => "int"
                ),
            "damage" => array(
                "field" => "damage",
                "type" => "int"
                )    

            );
    
    #========================================================================
    #   General DAO Methods
    #========================================================================
    
    #-------------
    #   getTsunamiSocialScienceData 
    #
    function getTsunamiSocialScienceData($id=null){
      if (is_null($id))
         return $this->setError("id must have a value and cannot be NULL.");

        $obj = $this->newTsunamiSocialScienceData();
        $obj->setId($id);
	    if($this->find($obj)) return $obj;
        else{
            $this->Error = "Could not find TsunamiSocialScienceData with id of '$id'.";
            return false;
        }
    }
    
    #-------------
    #   newTsunamiSocialScienceData
    #
    function newTsunamiSocialScienceData(){
        $obj = new TsunamiSocialScienceData();
        #   set the object's DAO to this for reference
        $obj->setDAO($this);
        return $obj;;
    }


    #-----------
    #   list
    #
    function listTsunamiSocialScienceData( &$filter = null, $expanded = false, $order = false){

        $meta = $this->metadata;
        
        if ($filter) {
            
			if($filter->setMetaData($this->metadata)==false)
				return($this->setError($filter->getError()));
			if(($str = $filter->getFilterString())==false)
				return($this->setError($filter->getError()));
			
			$where = " WHERE ".$str;
		}
		else $where = "";
        
        if(!$order) $order = "TsunamiSocialScienceDataId";
        
        $sql = "SELECT * FROM TsunamiSocialScienceData $where ORDER BY $order";
        
        $ret = $this->queryMyDAO($sql);
        if (!$ret) return false;
        	
        $results = array();
        
        if (count($ret) == 0) return ($results);
        
        if ($expanded){
            foreach($ret as $row){
                $results[$row["TsunamiSocialScienceDataId"]] = $this->getTsunamiSocialScienceData($row["TsunamiSocialScienceDataId"]);
            }             
        }else{
            foreach($ret as $row){
                $results[$row["TsunamiSocialScienceDataId"]] = $row["TsunamiSocialScienceDataId"];
            }
        }
        
        return $results;
    }
  
    #========================================================================
    #   Special DAO Methods
    #========================================================================


    function listTsunamiSocialScienceDataBySite($siteId,&$filter = null,
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

      $where.="TsunamiSocialScienceData.TsunamiDocLibId = ".
              "TsunamiSiteDocRelationship.TsunamiDocLibId ".
              "AND TsunamiSiteDocRelationship.TsunamiSiteId = $siteId ";

      if(!$order) $order = "TsunamiSocialScienceDataId";

      $sql = "SELECT * FROM TsunamiSocialScienceData, TsunamiSiteDocRelationship $where ORDER BY $order";

//		print "SQL: $sql <br/>";

      $ret = $this->queryMyDAO($sql);
      if (!$ret) return false;


      $results = array();

      if (count($ret) == 0) return ($results);


      if ($expanded){
         foreach($ret as $row) {
            $results[$row["TsunamiSocialScienceDataId"]] =
               $this->getTsunamiSocialScienceData($row["TsunamiSocialScienceDataId"]);
         }
      }

      else {
         foreach($ret as $row) {
            $results[$row["TsunamiSocialScienceDataId"]] =
               $row["TsunamiSocialScienceDataId"];
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
        $res = $this->queryMyDAO("select * from TsunamiSocialScienceData where TsunamiSocialScienceDataId = $objID");
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
            $this->Error = "TsunamiSocialScienceData cound not be found.";
            return false;
        }
    }

	 function delete(&$obj){
		if( ($objId=$obj->getId())==-1)
			return $this->setError("Invalid ID for deletion");

		$sql = "delete from TsunamiSocialScienceData where TsunamiSocialScienceDataId = $objId;";
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
        $sql = "insert into TsunamiSocialScienceData SET $fields;";
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
            $this->Error = "TsunamiSocialScienceData id not set.";
            return false;
        }
        
        #   Check for bad id
        $checkId = $this->queryMyDAO("select TsunamiSocialScienceDataId as id from TsunamiSocialScienceData WHERE TsunamiSocialScienceDataId = $objId;");
			if ($checkId == false)
				return false;

        if ($checkId[0]["id"] != $objId){
            $this->Error = "Bad TsunamiSocialScienceData id.";
            return false;
        }
        
        #   Get fields and values
        $fields = $this->getSQLSet($obj);
        
        #   Execute the sql
        $sql = "update TsunamiSocialScienceData SET $fields WHERE TsunamiSocialScienceDataId = $objId;";
                
        if($this->execMyDAO($sql)==false)
				return false;
        
        return true;
    }
  
}

?>
