<?php

require_once 'lib/data/om/BaseCoordinatorRun.php';
require_once 'lib/data/CoordinatorRunExperiment.php';


/**
 * Skeleton subclass for representing a row from the 'COORDINATOR_RUN' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class CoordinatorRun extends BaseCoordinatorRun {

  /**
   * get the list of viewable PhysicalSubstructures that belong to this Coordinator-Run
   *
   * @param String $orderby
   * @return array <Experiment>
   */
  public function getPhysicalSubstructures($orderby=null) {
    return self::getSubstructures("physical");
  }


  /**
   * get the list of viewable AnalyticalSubstructures that belong to this Coordinator-Run
   *
   * @param String $orderby
   * @return array <Experiment>
   */
  public function getAnalyticalSubstructures($orderby=null) {
    return self::getSubstructures("analytical");
  }


  /**
   * get the list of viewable Substructures (Physical or Analytical) that belong to this Coordinator-Run
   *
   * @param String $substructureType ("physical" | "analytical")
   * @param String $orderby
   * @return array <Experiment>
   */
  public function getSubstructures($substructureType="", $orderBy=null) {

    if($substructureType == "physical") {
      $coordinatorRunExperiments = CoordinatorRunExperimentPeer::findByCoordinatorRunAndPhysicalType($this->getId(), $orderBy);
    }
    elseif($substructureType == "analytical") {
      $coordinatorRunExperiments = CoordinatorRunExperimentPeer::findByCoordinatorRunAndAnalyticalType($this->getId(), $orderBy);
    }
    else {
      $coordinatorRunExperiments = CoordinatorRunExperimentPeer::findByCoordinatorRun($this->getId(), $orderBy);
    }

    $auth = Authorizer::getInstance();

    $list = array();
    foreach($coordinatorRunExperiments as $ce) {
      $substructure = $ce->getExperiment();

      if($substructure->isPublished() || $auth->canView($substructure)) {
        $list[] = $ce->getExperiment();
      }
    }

    return $list;
  }



  /**
   * Each CoordinatorRun is associated with a directory on disk. This function returns the path of that directory for this CoordinatorRun.
   *
   * @return String $path
   */
  public function getPathname() {
    return $this->getCoordinator()->getPathname() . '/CoordinatorRun-' . $this->getId();
  }

} // CoordinatorRun
