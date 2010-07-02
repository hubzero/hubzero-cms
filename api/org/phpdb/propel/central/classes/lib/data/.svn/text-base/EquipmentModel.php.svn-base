<?php

require_once 'lib/data/om/BaseEquipmentModel.php';


/**
 * EquipmentModel
 *
 * Describes a particular kind of Equipment for use in an experiment
 *
 *
 *
 *@package    lib.data
 *
 * @uses EquipmentClass
 * @uses DataFile
 */
class EquipmentModel extends BaseEquipmentModel {

  /**
   * Initializes internal state of EquipmentModel object.
   */
  function __construct(EquipmentClass $equipmentClass=null,
                       $name=null,
                       $manufacturer=null,
                       $supplier=null,
                       $modelNumber=null,
                       DataFile $additionalSpecFile=null,
                       DataFile $manufacturerDocFile=null,
                       DataFile $designConsiderationFile=null,
                       DataFile $subcomponentsDocFile=null,
                       DataFile $interfaceDocFile=null,
                       $additionalSpecPageCount=null,
                       $manutacturerDocPageCount=null,
                       $designConsiderationPageCount=null,
                       $subcomponentsDocPageCount=null,
                       $interfaceDocPageCount=null
                       ) {
    $this->setEquipmentClass($equipmentClass);
    $this->setName($name);
    $this->setManufacturer($manufacturer);
    $this->setSupplier($supplier);
    $this->setModelNumber($modelNumber);
    $this->setDataFileRelatedByAdditionalSpecFileId($additionalSpecFile);
    $this->setDataFileRelatedByManufacturerDocFileId($manufacturerDocFile);
    $this->setDataFileRelatedByDesignConsiderationFileId($designConsiderationFile);
    $this->setDataFileRelatedBySubcomponentsDocFileId($subcomponentsDocFile);
    $this->setDataFileRelatedByInterfaceDocFileId($interfaceDocFile);
    $this->setAdditionalSpecPageCount($additionalSpecPageCount);
    $this->setManufacturerDocPageCount($manutacturerDocPageCount);
    $this->setDesignConsiderationPageCount($designConsiderationPageCount);
    $this->setSubcomponentsDocPageCount($subcomponentsDocPageCount);
    $this->setInterfaceDocPageCount($interfaceDocPageCount);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/EquipmentModel/{$this->getId()}";
  }


  /**
   * Get AdditionalSpecFile
   * Backward compatible with NEEScentral
   *
   * @return DataFile
   */
  public function getAdditionalSpecFile() {
    return $this->getDataFileRelatedByAdditionalSpecFileId();
  }


  /**
   * Set AdditionalSpecFile
   * Backward compatible with NEEScentral
   *
   * @param DataFile $df: the AdditionalSpecFile
   */
  public function setAdditionalSpecFile($df) {
    return $this->setDataFileRelatedByAdditionalSpecFileId($df);
  }



  /**
   * Get ManufacturerDocFile
   * Backward compatible with NEEScentral
   *
   * @return DataFile
   */
  public function getManufacturerDocFile() {
    return $this->getDataFileRelatedByManufacturerDocFileId();
  }


  /**
   * Set ManufacturerDocFile
   * Backward compatible with NEEScentral
   *
   * @param DataFile $df: the ManufacturerDocFile
   */
  public function setManufacturerDocFile($df) {
    return $this->setDataFileRelatedByManufacturerDocFileId($df);
  }



  /**
   * Get DesignConsiderationFile
   * Backward compatible with NEEScentral
   *
   * @return DataFile
   */
  public function getDesignConsiderationFile() {
    return $this->getDataFileRelatedByDesignConsiderationFileId();
  }


  /**
   * Set DesignConsiderationFile
   * Backward compatible with NEEScentral
   *
   * @param DataFile $df: the DesignConsiderationFile
   */
  public function setDesignConsiderationFile($df) {
    return $this->setDataFileRelatedByDesignConsiderationFileId($df);
  }


  /**
   * Get SubcomponentsDocFile
   * Backward compatible with NEEScentral
   *
   * @return DataFile
   */
  public function getSubcomponentsDocFile() {
    return $this->getDataFileRelatedBySubcomponentsDocFileId();
  }


  /**
   * Set SubcomponentsDocFile
   * Backward compatible with NEEScentral
   *
   * @param DataFile $df: the SubcomponentsDocFile
   */
  public function setSubcomponentsDocFile($df) {
    return $this->setDataFileRelatedBySubcomponentsDocFileId($df);
  }



  /**
   * Get InterfaceDocFile
   * Backward compatible with NEEScentral
   *
   * @return DataFile
   */
  public function getInterfaceDocFile() {
    return $this->getDataFileRelatedByInterfaceDocFileId();
  }


  /**
   * Set InterfaceDocFile
   * Backward compatible with NEEScentral
   *
   * @param DataFile $df: the InterfaceDocFile
   */
  public function setInterfaceDocFile($df) {
    return $this->setDataFileRelatedByInterfaceDocFileId($df);
  }


  /**
   * Each EquipmentModel is associated with a directory on disk.
   * This function returns the path of that directory for
   * this EquipmentModel.
   */
  public function getPathname() {
    return "/nees/home/facility.groups/EquipmentModel/Model" . $this->getId();
  }


} // EquipmentModel
?>
