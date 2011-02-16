<?php

require_once 'lib/data/om/BaseTrial.php';
require_once 'lib/data/Repetition.php';


/**
 * Trial
 *
 * An {@link Experiment} may consist of several Trials
 *
 * @package    lib.data
 *
 * @uses Experiment
 * @uses MeasurementUnit
 * @uses DataFile
 *
 */
class Trial extends BaseTrial {

  /**
   * Constructs a new Trial object,
   * setting the trial_type_id column to TrialPeer::CLASSKEY_TRIAL.
   */
  function __construct( Experiment $experiment = null,
                        $name = "", 
                        $title = "",
                        $objective = "",
                        $description = "",
                        $startDate = null,
                        $endDate = null,
                        $status = "",
                        $acceleration = null,
                        $baseAcceleration = null,
                        MeasurementUnit $baseAccelerationUnit = null,
                        $motionName = "",
                        $station = "",
                        $component = "",
                        DataFile $motionFile = null ,
                        $curationstatus = "Uncurated",
                        $deleted = false)
  {

    $this->setExperiment($experiment);
    $this->setName($name);
    $this->setDescription($description);
    $this->setObjective($objective);
    $this->setTitle($title);
    $this->setStartDate($startDate);
    $this->setEndDate($endDate);
    $this->setAcceleration($acceleration);
    $this->setBaseAcceleration($baseAcceleration);
    $this->setMeasurementUnit($baseAccelerationUnit);
    $this->setMotionName($motionName);
    $this->setStation($station);
    $this->setComponent($component);
    $this->setDataFile($motionFile);
    $this->setStatus($status);
    $this->setCurationStatus($curationstatus);
    $this->setDeleted($deleted);

    $this->setTrialTypeId(TrialPeer::CLASSKEY_TRIAL);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $exp = $this->getExperiment();
    return $exp->getRESTURI() . "/Trial/{$this->getId()}";
  }

	/**
	 * Get the associated MeasurementUnit object
	 * Wraps {@link BaseTrial::getMeasurementUnit()}
	 *
	 * @param      Connection Optional Connection object.
	 * @return     MeasurementUnit The associated MeasurementUnit object.
	 * @throws     PropelException
	 */
	public function getBaseAccelerationUnit($con = null) {
    $this->getMeasurementUnit($con);
  }


	/**
	 * Declares an association between this object and a MeasurementUnit object.
   *  Wraps {@link BaseTrial::setMeasurementUnit()}
	 *
	 * @param      MeasurementUnit $v
	 * @return     void
	 * @throws     PropelException
	 */
  public function setBaseAccelerationUnit(MeasurementUnit $mu) {
    $this->setMeasurementUnit($mu);
  }

	/**
	 * Get the associated Motion DataFile object
	 *
	 * @param      Connection Optional Connection object.
	 * @return     DataFile The associated DataFile object.
	 * @throws     PropelException
	 */
  public function getMotionFile($con = null) {
    return $this->getDataFile($con);
  }

	/**
	 * Declares an association between this object and a DataFile object.
	 * Wraps {@link BaseTrial::setDataFile() }
	 *
	 * @param      DataFile $v
	 * @return     void
	 * @throws     PropelException
	 */
  public function setMotionFile(DataFile $df) {
    $this->setDataFile($df);
  }


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this Trial has previously
	 * been saved, it will retrieve related ControllerConfigs from storage.
	 * If this Trial is new, it will return
	 * an empty collection or the current collection, the criteria
	 * is ignored on a new object.
	 *
	 * @param      Connection $con
	 * @param      Criteria $criteria
	 * @throws     PropelException
   * @todo why is this not plural?
	 */
	public function getControllerConfig($criteria = null, $con = null) {
	  return $this->getControllerConfigs($criteria,$con);
  }


  /**
   * Set the ControllerConfigs for this Trail
   * Backward compatible with NEEScentral 1.7
   *
   * @param array <ControllerConfig>
   */
  public function setControllerConfig($coll) {
    if(is_null($coll)) $coll = array();
    $this->collControllerConfigs = $coll;
  }


  /**
   * Set the DAQConfigs for this Trail
   * Backward compatible with NEEScentral 1.7
   *
   * @param array <DAQConfig>
   */
  public function setDAQConfigs($coll) {
    if(is_null($coll)) $coll = array();
    $this->collDAQConfigs = $coll;
  }


  /**
   * Set the Repetitions for this Trail
   * Backward compatible with NEEScentral 1.7
   *
   * @param array <DAQConfig>
   */
  public function setRepetitions($coll) {
    if(is_null($coll)) $coll = array();
    $this->collRepetitions = $coll;
  }


  /**
   * This function is the same with BaseTrial::getRepetitions()
   * However, it will remove any repetition that marked with deleted from the return list
   *
   * @return array <Repetition>
   */
  function getTrialRepetitions() {
    return RepetitionPeer::findByTrial($this->getId());
  }


  /**
   * Get the Repetiontion belong to this Trial given by its Name or ID
   *
   * @param String or int $repname_orid
   * @return Repetition
   */
  function getRepetition($repname_orid) {
    $rcoll = $this->getTrialRepetitions();

    foreach ($rcoll as $rep) {
      if (($rep->getName() === $repname_orid) || ($rep->getId() === $repname_orid)) {
        return $rep;
      }
    }

    return null;
  }


  /**
   * Check if this trial is published or not by checking its parent Experiment published status
   *
   * @return boolean value
   */
  public function isPublished(){
    return $this->getExperiment()->isPublished();
  }


  /**
   * Check if this Trial is in the curation status or not
   *
   * @return boolean value
   */
  public function isInCuration(){
    if (($this->getCurationStatus() == "Curated") || ($this->getCurationStatus()=="Submitted")) {
      return true;
    }
  }


  /**
   * Check if this Trial can export to a DataViewer or not
   *
   * @param String $viewer
   * @return boolean value
   */
  function canExportToDataViewer($viewer) {

    switch ($viewer) {
    case "n3dv":
      return $this->checkN3DVStatus();

    default:
      return false;
    }
  }


  /**
   * Check if this Trial has any DAQConfig that can export to a DataViewer or not
   *
   * @return boolean value
   */
  private function checkN3DVStatus() {
    foreach( $this->getDAQConfigs() as $config ) {
      if( $config->canExportToDataViewer('n3dv') ) {
        return true;  // It only takes one
      }
    }
    return false;
  }


  /**
   * Each trial is associated with a directory on disk.
   * This function returns the path of that directory for
   * this trial.
   *
   * @return String $path
   */
  public function getPathname() {
    return $this->getExperiment()->getPathname() . "/" . $this->getName();
  }



  /**
   * Get the HTML code for display Trial inforation footer in NEEScentral UI
   *
   * @return String html code
   */
  public function getTrialInfoFooter() {
    $domain_type = $this->getExperiment()->isSimulation() ? "Simulation Run" : "Trial";
    $info = <<<ENDHTML
      <span style="white-space: nowrap;"><strong>$domain_type ID:</strong>  {$this->getId()} </span>
      <span style="white-space: nowrap;">&nbsp;&nbsp;|&nbsp;&nbsp;<strong>Name:</strong> {$this->getName()} </span>
ENDHTML;
    return  $info;
  }


  /**
   * Get the String image name for the icon represent fr this Trial
   *
   * @return String image_name
   */
  function getTrialIcon() {
    $domain_type = $this->getExperiment()->isSimulation() ? "run" : "trial";
    return  "icon_" . $domain_type . ($this->isPublished() ? "_published" : "") . "_80x80.gif";
  }

  /**
   * Check if the current user can access to this experiment
   *
   * @return boolean value
   */
  function isVisibleToCurrentUser() {
    if($this->getDeleted()) return false;

    $exp = $this->getExperiment();
    return $exp->isVisibleToCurrentUser();
  }



  /**
   * A convienion way to call an trial or simulation-run
   *
   * @param boolean $is_lower_case
   * @return String
   */
  public function getDisplayName($is_lower_case = false) {
    if($is_lower_case) return $this->getExperiment()->isSimulation() ? "run" : "trial";
    else return $this->getExperiment()->isSimulation() ? "Run" : "Trial";
  }

  public function getDataFiles(){
    require_once 'lib/data/DataFilePeer.php';
    return DataFilePeer::getDataFilesByTrial($this->getId());
  }

  public function getDataFileLinkCount($p_bDirectory=1){
    require_once 'lib/data/DataFileLinkPeer.php';
    return DataFileLinkPeer::getCountByTrialId($this->getId(), $p_bDirectory);
  }


  /**
   * Return friendly toString function to display Project information
   *
   * @return int $id
   */
  public function toString() {
    return $this->getId();
  }
} // Trial
?>
