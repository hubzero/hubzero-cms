<?php

require_once 'lib/data/om/BaseExperimentModel.php';


/**
 * ExperimentModel
 * called "Models"
 * Example: autocad drawing describing the test setup
 * an understanding of the eneral test setup, where sensors are located.
 * elevations, plan views, just like construction drawings.
 *
 * @todo Document this class
 *
 * @package    lib.data
 *
 * @uses Experiment
 * @uses ExperimentModelType
 */
class ExperimentModel extends BaseExperimentModel {

  /**
   * Initializes internal state of ExperimentModel object.
   */
  function __construct(Experiment $experiment = null,
                       ExperimentModelType $modelType = null,
                       $name = "",
                       $description = "")
  {
    $this->setExperiment($experiment);
    $this->setExperimentModelType($modelType);
    $this->setName($name);
    $this->setDescription($description);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/ExperimentModel/{$this->getId()}";
  }

  /**
   * get the type for this experiment model
   * @return ExperimentModelType
   * @deprecated use {@link ExperimentModel::getExperimentModelType() }
   */
  public function getModelType() {
    return $this->getExperimentModelType();
  }

  /**
   * set the type for this experiment model
   * @param ExperimentModelType
   * @deprecated use {@link ExperimentModel::setExperimentModelType() }
   */
  public function setModelType(ExperimentModelType $v) {
    return $this->setExperimentModelType($v);
  }


  /**
   * Each ExperimentModel is associated with a directory on disk.
   * This function returns the path of that directory for
   * this ExperimentModel.
   */
  public function getPathname() {
    return $this->getExperiment()->getPathname() . '/Models' . $this->getId();
  }

} // ExperimentModel
?>
