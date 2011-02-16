<?php

require_once 'lib/data/om/BaseExperiment.php';
require_once 'lib/data/Organization.php';
require_once 'lib/data/Trial.php';
require_once 'lib/data/ExperimentMeasurement.php';
require_once 'lib/data/LocationPlan.php';
require_once 'lib/data/Acknowledgement.php';

/**
 * Experiment
 *
 * Represents a single experimental test. Associated with a {@link Project},
 * contains a number of different {@link Trial}s.
 *
 * Example: Running a shake table test of a two-story wood-framed house
 * one experiment could be suppose you build a gfirst floor and test it
 * What happens is, once you build the whole structure,
 * you might do some white noise tests just to get some readings
 * then you might run the el centro input motions.
 *
 * These are all *trials*
 *
 * The structure as configured stays 98% the same, only something small
 * changes, usually the loading protocol. For a differnet trial to happen means
 * you're getting a different output file from you data acquisition dsystem.
 * You might run the same loading, you're chaning the loading history.
 *
 * @package    lib.data
 *
 * @uses Project
 * @uses ExperimentDomain
 * 
 */
abstract class Experiment extends BaseExperiment {

  private static $isVisible = array();

  function __construct( Project $project = null,
                        $title = "",
                        $objective = "",
                        $description = "",
                        $startDate = null,
                        $endDate = null,
                        $status = "unpublished",
                        $view = "MEMBERS",
                        ExperimentDomain $domain = null,
                        $curationstatus="Uncurated",
                        $deleted = 0)
  {
    $this->setProject($project);
    $this->setTitle($title);
    $this->setObjective($objective);
    $this->setDescription($description);
    $this->setStartDate($startDate);
    $this->setEndDate($endDate);
    $this->setStatus($status);
    $this->setView($view);
    $this->setExperimentDomain($domain);
    $this->setCurationStatus($curationstatus);
    $this->setDeleted($deleted);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/Project/{$this->getProject()->getId()}/Experiment/{$this->getId()}";
  }

  /**
   * Sets the sensorLocationPlans associated with this Experiment
   * @param lib.domain.Collection the new set of SensorLocationPlan
   * @deprecated
   */
  public function setSensorLocationPlans($slps) {
    $this->collLocationPlans = array();
    foreach ($slps as $slp) {
      $this->addLocationPlan($slp);
    }
  }


  /**
   * Gets the sensorLocationPlans associated with this Experiment
   *
   * @return array <SensorLocationPlan>
   */
  public function getSensorLocationPlans() {
    return LocationPlanPeer::findSensorLocationPlanByExperiment($this->getId());
  }



  /**
   * Gets the SourceLocationPlans associated with this Experiment
   *
   * @return array <SourceLocationPlan>
   */
  public function getSourceLocationPlans() {
    return LocationPlanPeer::findSourceLocationPlanByExperiment($this->getId());
  }



  /**
   * Adding one more SensorLocationPlan to the list of LocationPlan Array
   *
   * @param SensorLocationPlan $slp
   */
  public function addSensorLocationPlan(SensorLocationPlan $slp) {
    $this->addLocationPlan($slp);
  }



  /**
   * Remove a SensorLocationPlan from the list
   *
   * @param SensorLocationPlan $slp
   */
  public function removeSensorLocationPlan(SensorLocationPlan $slp) {
    //$this->getSensorLocationPlans()->remove($slp);
    if($slp === null) return;

    $old_list = $this->collLocationPlans;
    $new_list = array();

    foreach($old_list as $lp) {
      if($lp->getId() != $slp->getId()) {
        $new_list[] = $lp;
      }
    }

    // Reset the list to the new list after removing
    $this->collLocationPlans = $new_list;
  }



  /**
   * Sets the CoordinateSpaces associated with this Experiment
   * @param lib.domain.Collection the new set of CoordinateSpaces
   * @deprecated
   */
  public function setCoordinateSpaces($spaces) {
    $this->collCoordinateSpaces = array();
    foreach ($spaces as $space) {
      $this->addCoordinateSpace($space);
    }
  }


  /**
   * Sets the Trials associated with this Experiment
   * @param lib.domain.Collection the new set of Trials
   * @deprecated
   */
  public function setTrials($trials) {
    $this->collTrials = array();
    foreach ($trials as $t) {
      $this->addTrial($t);
    }
  }


  /**
   * Sets the ExperimentModels associated with this Experiment
   * @param lib.domain.Collection the set of Experiment Models
   * @deprecated
   */
  public function setExperimentModels($experiment_models) {
    $this->collExperimentModels = array();
    foreach ($experiment_models as $em) {
      $this->addExperimentModel($em);
    }
  }


  /**
   * Remove an ExperimentModel from the list
   *
   * @param ExperimentModel $em
   */
  function removeExperimentModel( ExperimentModel $em ) {
    if($em === null) return;

    $old_list = $this->collExperimentModels;
    $new_list = array();

    foreach($old_list as $m) {
      if($m->getId() != $em->getId()) {
        $new_list[] = $m;
      }
    }

    // Reset the list to the new list after removing
    $this->collExperimentModels = $new_list;
  }


  /**
   * Get the Acknowledgement String associated with this Experiment.
   *
   * @return String Acknowledgement (Sponsor column in database)
   */
  public function getExperimentAcknowledgement() {
    $ack = AcknowledgementPeer::findByExperiment($this->getId());
    return is_null($ack) ? "" : $ack->getSponsor();
  }


  /**
   * Set the Project Acknownledgement
   *
   * @param String $ack Acknowledgement
   */
  public function setExperimentAcknowledgement($ack) {

    $ack = substr(htmlspecialchars(trim($ack)), 0, 4000);

    $acknowledgement = AcknowledgementPeer::findByExperiment($this->getId());

    if(! empty($ack)) {
      if(is_null($acknowledgement)) {
        $acknowledgement = new Acknowledgement(null, $this, null, $ack, null);
      }
      else {
        $acknowledgement->setSponsor($ack);
      }

      $acknowledgement->save();
    }
    // Empty ack
    elseif(!is_null($acknowledgement)) {
      $acknowledgement->delete();
    }
  }


  /**
   * Determine if this is a structured or unstructured experiment
   * @return Boolean value, true if it is not UnstructuredExperiment, else false
   */
  public function getStructure() {
    return $this->getExperimentTypeId() != ExperimentPeer::CLASSKEY_UNSTRUCTUREDEXPERIMENT;
  }

  /**
   * Alias of getStructure
   */
  public function isStructured() {
    return $this->getStructure();
  }


  /**
   * Check if the current user can access to this experiment
   *
   * @return boolean value
   */
  function isVisibleToCurrentUser() {
    $expid = $this->getId();

    if( array_key_exists("$expid", self::$isVisible)) {
      return self::$isVisible["$expid"];
    }

    $proj = $this->getProject();

    if($this->getDeleted() || !$proj || $proj->getDeleted()) $ret = false;
    elseif ($this->isPublished()) $ret = true;
    elseif(Authenticator::getInstance()->isLoggedIn() && (Authorizer::getInstance()->canView($this))) $ret = true;
    else $ret = false;

    self::$isVisible["$expid"] = $ret;
    return $ret;
  }


  /**
   * Get the type name of this class
   *
   * @return String
   */
  public function getEntityTypeName() {
    return "Experiment";
  }


  /**
   * Overwrite parent::getCurationStatus() because the column Curation_Status currently not updated
   *  @return String CurationStatus
   */
  public function getCurationStatus() {

    $curatedObject = NCCuratedObjectsPeer::findByExperimentId($this->getId());
    if(is_null($curatedObject)) {
      return "Uncurated";
    }
    return $curatedObject->getCurationState();
  }


  /**
   * Get the Unit defined for this experiment given by a category
   *
   * @param MeasurementUnitCategory $category
   * @return MeasurementUnit
   */
  function getUnit(MeasurementUnitCategory $category){
    $expUnit = ExperimentMeasurementPeer::findByExperimentAndCategory($this->getId(),$category->getId());
    if($expUnit){
      return $expUnit->getDefaultUnit();
    }
    else{
      return null;
    }
  }


  /**
   * Get all the ExperimentMeasurement defined for this experiment
   *
   * @return array <ExperimentMeasurement>
   */
  function getUnits(){
    return ExperimentMeasurementPeer::findByExperiment($this->getId());
  }


  /**
   * Get the Trial in this experiment given by a trialname or trialid
   *
   * @param $trialname (or $trialid)
   * @return Trial
   */
  function getTrial($trialname) {
    $tcoll = $this->getTrials();
    foreach ($tcoll as $trial) {
      if (! $trial->getDeleted() && (($trial->getName() == $trialname) || ($trial->getId() == $trialname))) {
        return $trial;
      }
    }
    return null;
  }


  /**
   * Check if this experiment associate with a given Facility
   *
   * @param Facility $facility to check
   * @return boolean value
   */
  public function hasFacility(Facility $facility) {
    $facilities = $this->getFacilities();

    foreach($facilities as $fac) {
      if ($fac->getId() == $facility->getId())
      return true;
    }
    return false;

  }


  /**
   * Check if this experiment has any Facility associate with
   *
   * @return boolean value
   */
  public function hasFacilities() {
    return  count($this->getFacilities()) > 0;
  }


  /**
   * Add a facility to the list of facilities that associate with this experiment
   *
   * @param Facility $facility
   */
  public function addFacility(Facility $facility) {
    $facility->addExperiment($this);
  }


  /**
   * Remove a facility from the list of facilities that associate with this experiment
   *
   * @param Facility $facility
   */
  public function removeFacility(Facility $facility) {
    $facility->removeExperiment($this);
  }


  /**
   * Get the list off facilities that associate with this experiment
   *
   * @return array <Facility>
   */
  public function getFacilities() {
    return FacilityPeer::findByExperiment($this->getId());
  }


  /**
   *  Manage ExperimentOrganization M2M
   **/

  /**
   * Check that an organization is associated with this Experiment
   *
   * @param Organization $org
   * @return boolean value
   */
  public function hasOrganization(Organization $org) {
    $criteria = new Criteria();
    $criteria->add(ExperimentOrganizationPeer::ORGID, $org->getId());
    return $this->countExperimentOrganizations($criteria) > 0;
  }


  /**
   * Add an Organization to the list of Organizations that associate with this experiment
   *
   * @param Organization $org
   */
  public function addOrganization(Organization $org) {
    if (!$this->hasOrganization($org)) {
      $eo = new ExperimentOrganization($this,$org);
      $eo->save();
      $this->addExperimentOrganization($eo);
      return $eo;
    }
  }


  /**
   * Remove an Organization from the list of Organizations that associate with this experiment
   *
   * @param Organization $org
   */
  public function removeOrganization(Organization $org) {
    if ($this->hasOrganization($org)) {
      $eo = ExperimentOrganizationPeer::findByExperimentOrganization($this->getId(), $org->getId());
      $eo->delete();
    }
  }


  /**
   * Get the list off Organizations that associate with this experiment
   *
   * @return array <Organization>
   */
  public function getOrganizations(Criteria $c = null, Connection $conn = null) {
    return OrganizationPeer::findByExperiment($this->getId());
  }


  /**
   * Get the list of ExperimentOrganization object
   *
   * @return array <ExperimentOrganization>
   *
   * @todo make use of criteria and connection...
   */
  public function getExperimentOrganizations($criteria = null, $con = null) {
    return ExperimentOrganizationPeer::findByExperiment($this->getId());
  }

  /** END ExperimentOrganization M2M **/

  private $cachedSensors = null;

  /**
   * Returns an array of all of the sensors available at all of this experiment's organizations and facilities.
   *
   * @return Array <Sensor>
   */
  public function getAllAvailableSensors() {
    if ($this->cachedSensors) {
      return $this->cachedSensors;
    }

    $this->cachedSensors = array();
    $sensorManifests = SensorManifestPeer::findByExperiment($this->getId());
    foreach ($sensorManifests as $manifest) {
      $transducers = $manifest->getSensors();
      foreach ($transducers as $transducer) {
        $this->cachedSensors[] = $transducer;
      }
    }
    return $this->cachedSensors;
  }


  /**
   * Check if this experiment is public or not
   *
   * @return boolean value
   */
  public function isPublished(){
    return ( $this->getView() == "PUBLIC");
  }


  /**
   * Check if this experiment is curated or not
   *
   * @return boolean value
   */
  public function isCurated(){
    return ($this->getCurationStatus() == "Curated");
  }


  /**
   * Check if this experiment is in the status of curation or not
   *
   * @return boolean value
   */
  public function isInCuration(){
    return (($this->getCurationStatus() == "Curated") || ($this->getCurationStatus()=="Submitted"));
  }


  /**
   * Check if this experiment can export to DataViewer or not
   *
   * @param String type $viewer
   * @return boolean value
   */
  public function canExportToDataViewer($viewer) {

    switch($viewer) {

      case "n3dv":
        foreach( $this->getTrials() as $t ) {
          if( (! $t->getDeleted()) && $t->canExportToDataViewer($viewer) ) {
            return true;  // It just takes one
          }
        }
        return false;

      default:
        return false;

    }
  }


  /**
   * Each experiment is associated with a directory on disk.
   * This function returns the path of that directory for
   * this experiment.
   */
  public function getPathname() {
    return $this->getProject()->getPathname() . '/' . $this->getName();
  }


  /**
   * Check if this experiment is a type of Simulation or not
   *
   * @return boolean value
   */
  public function isSimulation() {
    return self::getExperimentTypeId() == ExperimentPeer::CLASSKEY_SIMULATION;
  }


  /**
   * A convienion way to call an experiment or simulation
   *
   * @param boolean $is_lower_case
   * @return String
   */
  public function getDisplayName($is_lower_case = false) {
    if($is_lower_case) return self::isSimulation() ? "simulation" : "experiment";
    else return self::isSimulation() ? "Simulation" : "Experiment";
  }


  /**
   * Get the Experiment Info footer that print out to the central
   *
   * @return html
   */
  public function getExperimentInfoFooter() {

    $domainType = $this->getExperimentDomain()->getDisplayName();

    $info = <<<ENDHTML

          <span style="white-space: nowrap;"><strong>Experiment ID:</strong> {$this->getId()} </span>
          <span style="white-space: nowrap;">&nbsp;&nbsp;|&nbsp;&nbsp;<strong>Name:</strong> {$this->getName()} </span>
          <span style="white-space: nowrap;">&nbsp;&nbsp;|&nbsp;&nbsp;<strong>Curation Status:</strong> {$this->getCurationStatus()} </span>
          <span style="white-space: nowrap;">&nbsp;&nbsp;|&nbsp;&nbsp;<strong>Domain:</strong> $domainType</span>
          <br/>

ENDHTML;

    return  $info;
  }


  /**
   * Get the default icon represent this experiment on Central UI
   *
   * @return String image icon
   */
  function getExperimentIcon() {

    $domain_type = $this->isSimulation() ? "simulation" : "experiment";

    if($this->isPublished()) {
      return "icon_" . $domain_type . "_published_80x80.gif";
    }
    else {
      return "icon_" . $domain_type . "_80x80.gif";
    }
  }


  /**
   * Get the Experiment thumbnail that uploaded to Central by owner
   *
   * @return DataFile an image data file for this experiment
   */
  function getExperimentThumbnailDataFile() {
    return DataFilePeer::findThumbnailDataFile($this->getId(), 3);
  }


  function getExperimentThumbnailHTML() {
    $default_thumbnail = "";

    $expImage = $this->getExperimentThumbnailDataFile();

    $thumbnail = null;
    if($expImage && file_exists($expImage->getFullPath())) {
      //creates the thumbnail if it doesn't exist
      $expThumbnailId = $expImage->getImageThumbnailId();

      if($expThumbnailId && $expThumbnail = DataFilePeer::find($expThumbnailId)) {
        if(file_exists($expThumbnail->getFullPath())) {
          $strDisplayName = "display_".$expImage->getId()."_".$expImage->getName();
          $expImage->setName($strDisplayName);
          $expImage->setPath($expThumbnail->getPath());
          $thumbnail = "<div class='thumb_frame'><a title='".$expImage->getDescription()."' style='border-bottom:0px;' target='_blank' href='" . $expImage->getUrl() . "' rel='lightbox[experiments]'><img src='" . $expThumbnail->get_url() . "'  alt='".$expImage->getDescription()."'/></a></div>";
        }
      }
    }

    if(!$thumbnail) $thumbnail = $default_thumbnail;

    return $thumbnail;
  }

  /**
   * Get the Experiment thumbnail that uploaded to Central by owner
   *
   * @return Array of thumbnail information (name, path, url)
   */
  function getExperimentThumbnailListing() {
    $expImage = $this->getExperimentThumbnailDataFile();

    $thumbnail = null;
    if($expImage && file_exists($expImage->getFullPath())) {
      $expThumbnailId = $expImage->getImageThumbnailId();

      if($expThumbnailId && $expThumbnail = DataFilePeer::find($expThumbnailId)) {
        if(file_exists($expThumbnail->getFullPath())) {
          $thumbnail = "<div style='width:60px; height:60px;'><a onClick='move_to_experiment=false;' onmouseout='move_to_experiment=true;' target='_blank' href='" . $expImage->get_url() . "'><img src='" . $expThumbnail->get_url() . "' alt='' /></a></div>";
        }
      }
    }
    return $thumbnail;
  }

  function getExperimentIndeedFile($p_strTool, $p_iProjectId, $p_iExperimentId){
    return DataFilePeer::findDataFileByTool($p_strTool, $p_iProjectId, $p_iExperimentId);
  }

  /**
   * Return the Experiment Query, which helpful to create the link address for this Experiment
   *
   */
  function getExperimentQuery(){
    return $this->getProject()->getProjectQuery() . "&expid=" . $this->getId();
  }

  /**
   * Gets the experiment dates
   * @return start and end date
   */
  public function getDates(){
  	//if no start date, return empty string
    $strDates = trim($this->getStartDate()); 
    if(strlen($strDates) == 0){
      return $strDates;
    }

    //if we have start but no end date, enter Present
    if(strlen($this->getEndDate())>0){
      $strDates = strftime("%B %d, %Y", strtotime($strDates)) . " - ". strftime("%B %d, %Y", strtotime($this->getEndDate()));
      //$strDates = $strDates . " to ". $p_oExperiment->getEndDate();
    }else{
      //$strDates = $strDates . " to Present";
      $strDates = strftime("%B %d, %Y", strtotime($strDates)) . " to Present";
    }
    return $strDates;
  }

  public function hasOpenData(){
    return $this->isPublished();
  }

  public function getDataFileLinkCount($p_bDirectory=1){
    require_once 'lib/data/DataFileLinkPeer.php';
    return DataFileLinkPeer::getCountByExperimentId($this->getId(), $p_bDirectory);
  }

  /**
   * Return friendly toString function to display Project information
   *
   * @return int $id
   */
  public function toString() {
    return $this->getId();
  }
} // Experiment
?>
