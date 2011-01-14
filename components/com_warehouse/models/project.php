<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
include_once 'lib/data/curation/NCCuratedObjectCatalogEntryPeer.php';

class WarehouseModelProject extends WarehouseModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  public function getOntologyByProjectId($p_iProjectId){
  	return NCCuratedObjectCatalogEntryPeer::getOntologyByProjectId($p_iProjectId);
  }
	
  public function findProjectFacility($p_iProjectId){
  	return OrganizationPeer::findProjectFacility($p_iProjectId);
  	//return OrganizationPeer::findByProject($p_iProjectId);
  }
  
  public function findProjectEquipment($p_iProjectId){
  	return EquipmentPeer::findByProject($p_iProjectId);
  }
  
  /**
   * Find the publications to show in the project display.
   * @param $p_iUserId - current user's id
   * @param $p_strProjectName - project name in oracle (also group cn in mysql) 
   * @param $p_iLimit - how many rows to display
   * @return array
   */
  public function findProjectPublications($p_iUserId, $p_strProjectName, $p_iLimit){
    if(!$p_strProjectName || $p_strProjectName===""){
      return;
    }

    //make the name lower case for matching common name (cn) of jos_xgroups.
    $p_strProjectName = strtolower($p_strProjectName);

    //jos_xgroup's cn does not like hyphens.  replace hyphen with underscore.
    $p_strProjectName = str_replace("-",  "_",  $p_strProjectName);
  	  	
    $strQuery = "SELECT distinct(r.id), r.title 
                 FROM #__resources r
                 INNER JOIN #__xgroups g ON g.cn = r.group_owner
                 LEFT OUTER JOIN #__xgroups_members gm ON gm.gidNumber = g.gidNumber
                 LEFT OUTER JOIN #__users u ON u.id = gm.uidNumber
                 WHERE r.group_owner = '".$p_strProjectName."'
                   AND r.type = 3
                   AND (
                                (r.access = 0 OR u.id = $p_iUserId)
                        ) 
                ";
    if($p_iLimit > 0){
      $strQuery .= " LIMIT $p_iLimit";
    }

    $oReturnArray = array();

    $oDatabase =& JFactory::getDBO();
    $oDatabase->setQuery($strQuery);
    $oPublicationArray = $oDatabase->loadAssocList();
    foreach($oPublicationArray as $oPublication){
      $iPublicationId = $oPublication['id'];
      $strAuthorArray = $this->findResourceAuthors($iPublicationId);
      $oPublication['authors'] = $strAuthorArray;

      //append the publication array to the results
      array_push($oReturnArray, $oPublication);
    }
    return $oReturnArray;
  }
  
  public function findResourceAuthors($p_iPublicationId){
    if(!$p_iPublicationId){
      return;
    }

    //$strQuery = "select authorid, name from jos_author_assoc where subid=".$p_iPublicationId." order by authorid";
    $strQuery = "SELECT n.uidNumber AS id,
            a.name AS name,
            n.name AS xname,
            n.givenName AS firstname,
            n.middleName AS middlename,
            n.surname AS lastname,
            a.organization AS org,
            n.organization AS xorg,
            a.role, a.authorid
            FROM #__xprofiles AS n,
            #__author_assoc AS a
            WHERE n.uidNumber=a.authorid
            AND a.subtable='resources'
            AND a.subid=".$p_iPublicationId."
            ORDER BY ordering, surname, givenName, middleName";

    $oDatabase =& JFactory::getDBO();
    $oDatabase->setQuery($strQuery);
    return $oDatabase->loadAssocList();
	
  }
  
  /**
   * Find the publications to show in the project display.
   * @param $p_iUserId - current user's id
   * @param $p_strProjectName - project name in oracle (also group cn in mysql) 
   * @param $p_iLimit - how many rows to display
   * @return array
   */
  public function findProjectPublicationCount($p_iUserId, $p_strProjectName, $p_iLimit){
  	if(!$p_strProjectName || $p_strProjectName===""){
  	  return;
  	}
  	
  	//make the name lower case for matching common name (cn) of jos_xgroups.
  	$p_strProjectName = strtolower($p_strProjectName);
  	
  	//jos_xgroup's cn does not like hyphens.  replace hyphen with underscore.
  	$p_strProjectName = str_replace("-",  "_",  $p_strProjectName);
  	  	
    $strQuery = "SELECT count(distinct r.id) 
				 FROM #__resources r
				 INNER JOIN #__xgroups g ON g.cn = r.group_owner
				 LEFT OUTER JOIN #__xgroups_members gm ON gm.gidNumber = g.gidNumber
				 LEFT OUTER JOIN #__users u ON u.id = gm.uidNumber
				 WHERE r.group_owner = '".$p_strProjectName."'
				   AND r.type = 3 
				   AND (
                                (r.access = 0 OR u.id = $p_iUserId)
				   	) 
				";
  	
  	$oDatabase =& JFactory::getDBO();
  	$oDatabase->setQuery($strQuery);
  	
	return $oDatabase->loadResult();
  }
  
  public function findTools($p_iProjectId){
  	return ProjectPeer::findTools($p_iProjectId);
  }
  
  public function getProjectImage($p_iProjectId){
    require_once 'lib/data/DataFilePeer.php';

    return DataFilePeer::getProjectImage($p_iProjectId);
  }
 
}

?>