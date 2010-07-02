<?php

require_once 'lib/data/om/BaseMaterial.php';


/**
 * Material
 *
 * Describes a material used in an {@link Experiment}
 * Constrained by {@link MaterialType}
 * Associated with an {@link Experiment}
 *
 * @todo document better
 *
 * @package    lib.data
 *
 * @uses Experiment
 * @uses MaterialType
 *
 */
class Material extends BaseMaterial {

  function __construct(Experiment $experiment = null,
                       MaterialType $materialType = null,
                       $name = "",
                       $description = "",
                       Material $prototype = null )
  {
    $this->setExperiment($experiment);
    $this->setMaterialType($materialType);
    $this->setName($name);
    $this->setDescription($description);
    $this->setMaterialRelatedByPrototypeMaterialId($prototype);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/MaterialProperties/{$this->getId()}";
  }

  /**
   * Get the material basis of this material
   *
   * @return Material
   */
  public function getPrototype() {
    return $this->getMaterialRelatedByPrototypeMaterialId();
  }


  /**
   * Set the PrototypeMaterial for this Material
   *
   * @param Material $prototype
   */
  public function setPrototype($prototype) {
    return $this->setMaterialRelatedByPrototypeMaterialId($prototype);
  }


  /**
   * The flip side of {@link Material::getPrototype()}
   *
   * @return array<Material>
   */
  public function getChildren() {
    return $this->getMaterialsRelatedByPrototypeMaterialId();
  }

  /**
   * Get the material files for this Material
   *
   * @return array <MaterialFile>
   */
  public function getFiles() {
    return $this->getMaterialFilesJoinDataFile();
  }


  /**
   * Set the collection of MaterialFile for this Material
   *
   * @param $collFiles: array <MaterialFile>
   */
  public function setFiles($collFiles) {
    if(is_null($collFiles)) $collFiles = array();
    $this->collMaterialFiles = $collFiles;
  }

  /**
   * Get the list of Material properties
   *
   * @return array<MaterialProperties>
   */
  public function getMaterialProperties() {
    return $this->getMaterialPropertys();
  }


  /**
   * Set the collection of MaterialProperties for this Material
   *
   * @param $props: array <MaterialProperty>
   */
  public function setMaterialProperties($props) {
    if(is_null($props)) $props = array();
    $this->collMaterialPropertys = $props;
  }

  /**
   * 
   * @param <type> $con
   * @return <type>
   */
  public function  getMaterialType($con = null) {
    return parent::getMaterialType($con);
  }


  /**
   * Each Material is associated with a directory on disk.
   * This function returns the path of that directory for
   * this Material.
   *
   */
  public function getPathname() {
    // I see some Material have NULL Experiment, better to exit than do something stupid on files system
    $exp = $this->getExperiment();
    if(empty($exp)) exit("Experiment for this material doesn not found");

    return $this->getExperiment()->getPathname() . "/" . "Material" . $this->getId();
  }


} // Material
?>
