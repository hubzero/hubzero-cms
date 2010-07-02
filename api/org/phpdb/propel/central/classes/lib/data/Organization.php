<?php

require_once 'lib/data/om/BaseOrganization.php';
require_once 'lib/data/ProjectOrganization.php';
require_once 'lib/data/ExperimentOrganization.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/data/Equipment.php';
require_once 'lib/data/SensorManifest.php';


/**
 * Organization
 *
 * Organizations are collections of {@link Person}
 *
 * Organizations can be hierarchically ordered using the {@link parent}
 *
 * Organizations have a  {@link SensorManifest} describing the available {@link Sensor}s
 *
 * @package    lib.data
 *
 * @uses SensorManifest
 */
class Organization extends BaseOrganization {

  /**
   * Initializes internal state of Organization object.
   */
  function __construct($name = "",
                       $description = "",
                       $url = "",
                       SensorManifest $sensorManifest = null,
                       Organization $parent = null)
  {
    $this->setOrganizationTypeId(OrganizationPeer::CLASSKEY_ORGANIZATION);
    $this->setFacilityId(0);
    $this->setName($name);
    $this->setDescription($description);
    $this->setUrl($url);
    $this->setSensorManifest($sensorManifest);
    $this->setParentOrganization($parent);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/Organization/{$this->getId()}";
  }

  /**
   * Wrap {@link BaseOrganization::getOrganizationRelatedByParentId}
   * Get the Parent Organization of this Organization
   *
   * @return Organization
   */
  public function getParentOrganization() {
    return $this->getOrganizationRelatedByParentOrgId();
  }

  /**
   * Wrap {@link BaseOrganization::setOrganizationRelatedByParentId}
   * Set the Parent Organization of this Organization
   * Backward compatible to NEEScentral 1.7
   *
   * @param Organization $org
   */
  public function setParentOrganization($parent) {
    $this->setParentOrgId(is_null($parent) ? 0 : $parent->getId());
  }


  /**
   * Wrap {@link BaseOrganization::setOrganizationRelatedByParentId}
   * An alias of setParentOrganization
   * Set the Parent Organization of this Organization
   * Backward compatible to NEEScentral 1.7
   *
   * @param Organization $org
   */
  function setParent(Organization $org) {
    $this->setParentOrganization($org);
  }


  /**
   * Check if this organization is visible from the current user login to NEEScentral
   *
   * @return boolean value
   */
  function isVisibleToCurrentUser() {
    return true;
  }


############################# Organization - Project ################################

  /*********************************************
   * Many-to-many relationship with Project
   *********************************************/

  /**
   * Get Projects associated with this Organization
   * Through the ProjectOrganization relation
   *
   * @return array<Project>
   */
  public function getProjects(Criteria $c = null, Connection $conn = null) {
    $result = array();
    foreach ($this->getProjectOrganizationsJoinProject($c, $conn) as $po) {
      $result[] = $po->getProject();
    }
    return $result;
  }


 /**
  * Get the Project associate with this organization given by the Project name
  *
  * @param String $project_name
  * @return Project
  */
  public function getProject($project_name) {
    return ProjectPeer::findByOrganizationProjectName($this->getId(), $project_name);
  }


  /**
   * Add a project into the list of Projects associated with this Organization
   *
   * @param Project $project
   * @return the ProjectOrgaization Object
   */
  public function addProject(Project $project) {
    if(is_null($project)) return;

    if (!$this->hasProject($project)) {
      $po = new ProjectOrganization($project, $this);
      $po->save();
      $this->addProjectOrganization($po);

      return $po;
    }
  }


  /**
   * Remove a project from the list of Projects associated with this Organization
   *
   * @param Project $project
   */
  public function removeProject(Project $project) {
    if ($this->hasProject($project)) {
      $po = ProjectOrganizationPeer::findByProjectOrganization($project->getId(),$this->getId());
      $po->delete();
    }
  }


  /**
   * Checking if a project is associated with this Organization or not
	 *
   * @param Project $project
   * @return boolean value
   */
  public function hasProject(Project $project) {
    $criteria = new Criteria();
    $criteria->add(ProjectOrganizationPeer::PROJID, $project->getId());

    return $this->countProjectOrganizations($criteria) > 0;
  }



############################# Organization - Experiment ################################
  /*********************************************
   * Many-to-many relationship with Experiment
   *********************************************/

  /**
   * Get Experiment associated with this Organization
   * Through the ExperimentOrganization relation
   *
   * @return array<Experiment>
   */
  public function getExperiments(Criteria $c = null, Connection $conn = null) {
    $result = array();
    foreach ($this->getExperimentOrganizationsJoinExperiment($c, $conn) as $po) {
      $result[] = $po->getExperiment();
    }
    return $result;
  }



  /**
   * Checking if an experiment is associated with this Organization or not
   *
   * @param Experiment $exp
   * @return boolean value
   */
  public function hasExperiment(Experiment $exp) {
    $criteria = new Criteria();
    $criteria->add(ExperimentOrganizationPeer::EXPID, $exp->getId());

    return $this->countExperimentOrganizations($criteria) > 0;
  }


  /**
   * Add an Experiment into the list of experiments associated with this Organization
   *
   * @param Experiment $exp
   * @return the ExperimentOrgaization Object
   */
  public function addExperiment(Experiment $exp) {
    $exp->addOrganization($this);
  }


  /**
   * Remove an Experiment from the list of Experiments associated with this Organization
   *
   * @param Experiment $experiment
   */
  public function removeExperiment(Experiment $experiment) {
    return $experiment->removeOrganization($this);
  }


  /**
   * Get the list of Experiment belong to a Project and associated with this Organization
   *
   * @param Project $project
   * @return array <Experiment>
   */
  function getProjectExperiments( Project $project ) {
    return ExperimentPeer::findByProjectOrganization($project->getId(), $this->getId());
  }


  /**
   * Get the Experiment associated with this Organization given by the Experiment ID
   *
   * @param Project $project
   * @return array <Experiment>
   */
  function getExperiment($expid) {
    return ExperimentPeer::findByOrganizationExperimentId($this->getId(), $expid);
  }


############################# Organization - Sensor ################################

  /**
   * Get the list of Sensors associated with this Organization
   *
   * @todo find out why getSensorManifest is always null.
   */
  public function getSensors() {
    $sm = $this->getSensorManifest();

    if(is_null($sm)) throw new Exception("SensorManifest is null.");
    return $sm->getSensors();
  }


    /**
   * Get the Sensor from the list of Sensors of this organization, checked it with the Sensor ID
   *
   * @param int $sensorId
   * @return Sensor
   */
  function getSensor($sensorId) {
    $tcoll = $this->getSensors();
    foreach ($tcoll as $sensor) {
      if (($sensor->getId() == $sensorId)) {
        return $sensor;
      }
    }
    return null;
  }


  /**
   * Add a Sensor into the list of Sensors that belong to this Organization
   *
   * @param Sensor $sensor
   */
  function addSensor( Sensor $sensor ) {
    $this->getSensorManifest()->addSensor($sensor);
  }



  /**
   * Override to the parent class BaseOrganization::getSensorManifest()
   *
   * @return Sensormanifest
   */
  public function getSensorManifest($criteria = null, $con = null) {
    $sm = parent::getSensorManifest();

    // If we don't have an associated manifest already, make one.
    if ( $sm == null ) {
      $sm_id = $this->getSensorManifestId();

      if($sm_id) {
        $sm = SensorManifestPeer::find($sm_id);
        if($sm) return $sm;
      }

      $sm = new SensorManifest();
      $sm->setName($this->getName() . " Sensor List");
      $sm->save();
    }

    return $sm;

  }


############################# Organization - Equipment ################################

  /**
   * Wrap {@link BaseOrganization::getEquipments() } to suport legacy API
   *
   * @return array <Equipment> associated with this Organization
   */
  public function getEquipment(Criteria $c = null,Connection $conn= null ) {
    return $this->getEquipments($c,$conn);
  }

  /**
   * Set the array <Equipment> for this Organization
   *
   * @param $collEquip: array <Equipment>
   */
  public function setEquipment($collEquip) {
    if(is_null($collEquip)) $collEquip = array();
    $this->collEquipments = $collEquip;
  }


  /**
   * Get the list of Major Equipment in this Orgaization
   *
   * @return array [major]<Equipment>
   */
	function getMajorEquipment() {
		return EquipmentPeer::findAllMajorByOrganization($this->getId());
	}

  /**
   * Remove an equipment from the list of equipment for this organization
	 * @todo: Kevin, I think this code below not support for Central 1.8
   *
   * @param Equipment $equip
   */
  public function removeEquipment(Equipment $equip) {
  	$this->getEquipment()->remove($equip);
  }



############################# Organization - Others ################################


  /**
   * Print out information of facility
   *
   * @return string facility information
   */
  public function toString() {
    return
    "Organization ID: " . $this->getId() .
    ", Name: "          . $this->getName() .
    ", ShortName: "     . $this->getShortName() .
    ", ShortName: "     . $this->getShortName() .
    ", Is Facility: "   . $this->getOrganizationTypeId() ? "Yes":"No";
  }

} // Organization
?>
