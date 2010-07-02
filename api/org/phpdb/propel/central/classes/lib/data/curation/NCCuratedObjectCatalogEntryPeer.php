<?php

  // include base peer class
  require_once 'lib/data/curation/om/BaseNCCuratedObjectCatalogEntryPeer.php';

  // include object class
  include_once 'lib/data/curation/NCCuratedObjectCatalogEntry.php';


/**
 * Skeleton subclass for performing query and update operations on the 'CURATED_OBJECT_CATALOG_ENTRY' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data.curation
 */
class NCCuratedObjectCatalogEntryPeer extends BaseNCCuratedObjectCatalogEntryPeer {
 
  /**
   * 
   */
  public static function getOntologyByProjectId($p_iProjectId){
  	$strReturnArray = array();
  	$strQuery = "select coce.ontology_term 
				from curated_object_catalog_entry coce, 
					 curated_objects co, 
				     curatedncidcross_ref cr, 
				     project p 
				where p.projid = ? 
				  and cr.neescentral_objectid=p.projid 
				  and cr.curated_entityid = co.object_id 
				  and coce.object_id = co.object_id";
  	
  	$oConnection = Propel::getConnection();
    $oStatement = $oConnection->prepareStatement($strQuery);
    $oStatement->setInt(1, $p_iProjectId);
  	$oResultsSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
    while($oResultsSet->next()){
      array_push($strReturnArray, $oResultsSet->getString('ONTOLOGY_TERM'));
    }
    return $strReturnArray;
  }

  /**
   * 
   */
  public static function getOntologyByExperimentId($p_iExperimentId){
  	$strReturnArray = array();
  	$strQuery = "select coce.ontology_term 
				from curated_object_catalog_entry coce, 
					 curated_objects co, 
				     curatedncidcross_ref cr, 
				     experiment e 
				where e.expid = ? 
				  and cr.neescentral_objectid=e.expid 
				  and cr.curated_entityid = co.object_id  
				  and coce.object_id = co.object_id";
  	
  	$oConnection = Propel::getConnection();
    $oStatement = $oConnection->prepareStatement($strQuery);
    $oStatement->setInt(1, $p_iExperimentId);
  	$oResultsSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
    while($oResultsSet->next()){
      array_push($strReturnArray, $oResultsSet->getString('ONTOLOGY_TERM'));
    }
    return $strReturnArray;
  }
	
} // NCCuratedObjectCatalogEntryPeer
