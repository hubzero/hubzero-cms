<?php
require_once 'lib/data/curation/NCCuratedObjectsPeer.php';
//require_once 'api/org/nees/static/Experiments.php';
class CurationProgress {
	
	private $curatedProjectsMap = array();	
	private $projid;
	private $expid;
	private $type;
	private $foundProject = array();
	private $expObj = null;

	public function __construct() 
	{
    	$this->projid           = isset($_SESSION['projid'])            ? $_SESSION['projid']            : '22';//null;
    	if(JRequest::getVar('view')=='experiment'){
	  $expObj = unserialize($_REQUEST[Experiments::SELECTED]);
	}
    	$this->expid = isset($expObj->expid) ? $expObj->expid : null;
			
    	//$this->type = isset($_SESSION['expid']) ? $_SESSION['expid'] : null;
    	//$this->coordinatorId    = isset($_REQUEST['coordinatorId'])     ? $_REQUEST['coordinatorId']     : null;
    	//$this->coordinatorRunId = isset($_REQUEST['coordinatorRunId'])  ? $_REQUEST['coordinatorRunId']  : null;
    	//$this->specimenId       = isset($_REQUEST['specimenId'])        ? $_REQUEST['specimenId']        : null;
    	//$this->specCompId       = isset($_REQUEST['specCompId'])        ? $_REQUEST['specCompId']        : null;
  		//  $this->trialid          = isset($_REQUEST['trialid'])           ? $_REQUEST['trialid']           : null;
		//$this->sort	= 'projid';
    	$this->sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : null;
    	//$this->action = isset($_REQUEST['action'])  ? $_REQUEST['action']  : null;
    	// change action to 'searchresults'
    	//$this->curatedProjectsMap = NCCuratedObjectsPeer::getCuratedProjectsMap();
    		//$this->projectSpecimenMap = $this->getProjectSpecimenMap();
    	//$this->projectCoordinatorMap = $this->getProjectCoordinatorMap();

    	$this->selectedNodeID = "root";
  	}
  public function findCuratedObjHistoryByProjId($p_iProjectId)
  {
  	/*$strQuery = "select d.ID, d.PATH, d.NAME, d.DESCRIPTION, d.TITLE, 
  				co.description as CURATED_DESCRIPTION, co.TITLE as CURATED_TITLE   
				from data_file_link dfl, data_file d, curatedncidcross_ref cr, curated_objects co
				where dfl.proj_id = ?
				  and dfl.exp_id = ?
				  and dfl.trial_id = ?
				  and dfl.rep_id = ?
				  and dfl.id = d.id
				  and d.id = cr.neescentral_objectid
				  and cr.curated_entityid = co.object_id
				  and co.object_type = ?";
*/
  	$strQuery = "select d.created_date CREATE_DATE, d.curation_state CURATION_STATE, 
  	d.created_by CREATED_BY, d.comments COMMENTS from project a, curated_objects b, 
  	curatedncidcross_ref c, entity_curation_history d 
  	where a.projid='".$p_iProjectId."' and b.name=a.name AND c.curated_entityid=b.object_id and 
  	c.curated_entityid=d.object_id ORDER BY d.created_date DESC";
  	$oReturnArray = array();
  	//echo $strQuery;
  	$oConnection = Propel::getConnection();
    $oStatement = $oConnection->prepareStatement($strQuery);
    //$oStatement->setInt(1, $p_iProjectId);
    $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
    // I only need the first one, if you need the whole thing, replace 'if' with 'while'
    $title = '';
    if($oResultSet->next()){
  	  $strDrawingArray = array();
  	  $strDrawingArray['CREATE_DATE'] = $oResultSet->getString("CREATE_DATE");
  	  $strDrawingArray['CURATION_STATE'] = $oResultSet->getString("CURATION_STATE");
  	  $strDrawingArray['CREATED_BY'] = $oResultSet->getString("CREATED_BY");
  	  $strDrawingArray['COMMENTS'] = $oResultSet->getString("COMMENTS");
  	  $title = "<p>".$strDrawingArray['COMMENTS']."</p>";
  	  $title .= "<p>".$strDrawingArray['CURATION_STATE']." - ";
  	  $date_str = strftime("%m/%d/%Y", strtotime($strDrawingArray['CREATE_DATE']));
  	  $title .= $date_str."</p>";
  	  
  	  array_push($oReturnArray, $strDrawingArray);
  	}
  	//print_r($oReturnArray);
  	//return $oReturnArray;
  	return $title;
  }
  	
  public function findCuratedObjHistoryByExpId($expid)
  {
   	$strQuery = "select d.created_date CREATE_DATE, d.curation_state CURATION_STATE, d.created_by CREATED_BY, d.comments COMMENTS 
from experiment e, curated_objects b, curatedncidcross_ref c, entity_curation_history d 
where e.expid=".$expid."and b.object_type='Experiment' and c.curated_entityid=b.object_id 
and c.neescentral_objectid=e.expid  and c.curated_entityid=d.object_id
ORDER BY d.created_date DESC";
   	
   	$oReturnArray = array();
  	//echo $strQuery;
  	$oConnection = Propel::getConnection();
    $oStatement = $oConnection->prepareStatement($strQuery);
    //$oStatement->setInt(1, $p_iProjectId);
    $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
    // I only need the first one, if you need the whole thing, replace 'if' with 'while'
    $title = '';
    if($oResultSet->next()){
  	  $strDrawingArray = array();
  	  $strDrawingArray['CREATE_DATE'] = $oResultSet->getString("CREATE_DATE");
  	  $strDrawingArray['CURATION_STATE'] = $oResultSet->getString("CURATION_STATE");
  	  $strDrawingArray['CREATED_BY'] = $oResultSet->getString("CREATED_BY");
  	  $strDrawingArray['COMMENTS'] = $oResultSet->getString("COMMENTS");
  	  $title = "<p>".$strDrawingArray['COMMENTS']."</p>";
  	  $title .= "<p>".$strDrawingArray['CURATION_STATE']." - ";
  	  $date_str = strftime("%m/%d/%Y", strtotime($strDrawingArray['CREATE_DATE']));
  	  $title .= $date_str."</p>";
  	  
  	  array_push($oReturnArray, $strDrawingArray);
  	}
  	//print_r($oReturnArray);
  	//return $oReturnArray;
  	return $title;
  }
  
  
  public function getProjectCurationProgress()
  {		  
  	$oProject = unserialize($_REQUEST[Search::SELECTED]);
   	$title = $this->findCuratedObjHistoryByProjId($oProject->getId());
 	return $title;
  }

 public function getExperimentCurationProgress()
  {
	$oExperiment = unserialize($_REQUEST[Experiments::SELECTED]);
   	$title = $this->findCuratedObjHistoryByExpId($oExperiment->getId());
  	return $title;
  }
  
}
?>
