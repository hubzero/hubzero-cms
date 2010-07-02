<?php

require_once 'lib/data/om/BaseEquipment.php';


/**
 * Equipment
 *
 * Associate an experiment with a particular lab
 * Lab has an inventory of equipment
 * only that equipment is available, for selection from a list
 *
 * Equipment is stuff like:
 *   I did this experiment w/ Davis' Centrifuge, "I used bucket 17ca"
 *
 * @todo Lelli can't change the facility to UCSD as a NEES Facility
 *
 * @package    lib.data
 *
 * @uses EquipmentModel
 * @uses Organization
 */
class Equipment extends BaseEquipment {

  /**
   * Initializes internal state of Equipment object.
   */
  function __construct(  EquipmentModel $model=null,
                         Organization $organization=null,
                         Equipment $parent=null,
                         $major=0,
                         $neesOperated='NO',
                         $name=null,
                         $owner=null,
                         $separateScheduling=0,
                         $serialNumber=null,
                         $commissionDate=null,
                         $calibrationInformation=null,
                         $note=null,
                         $labAssignedId=null,
                         $quantity=null,
                         $deleted=0
                         ) {
		$this->setEquipmentModel($model);
    $this->setOrganization($organization);
    $this->setEquipmentRelatedByParentId($parent);
    $this->setMajor($major);
    $this->setNeesOperated($neesOperated);
    $this->setName($name);
    $this->setOwner($owner);
    $this->setSeparateScheduling($separateScheduling);
    $this->setSerialNumber($serialNumber);
    $this->setCommissionDate($commissionDate);
    $this->setCalibrationInformation($calibrationInformation);
    $this->setNote($note);
    $this->setLabAssignedId($labAssignedId);
    $this->setQuantity($quantity);
    $this->setDeleted($deleted);
  }


  /**
   * Get all Sub components for a Major Equipment
   *
   * @return array <Equipment>
   */
  public function getSubcomponents() {
		return EquipmentPeer::findAllByParent($this->getId());
	}

  /**
   * Get the Parent Equipment
   * Backward compatible with NEEScentral 1.7
   *
   * @return Equipment
   */
  public function getParent() {
    return $this->getEquipmentRelatedByParentId();
  }


  /**
   * Set the Parent Equipment
   * Backward compatible with NEEScentral 1.7
   *
   * @param Equipment: the parent equipment
   */
  public function setParent(Equipment $parent) {
    return $this->setEquipmentRelatedByParentId($parent);
  }


  /**
   * "Inverse" side of the ControllerChannelEquipment Relationship
   * @return array Array of ControllerChannel
   */
  public function getControllerChannels($criteria = null, $con = null) {
    $ccs = array();
    $cces = $this->getControllerChannelEquipmentsJoinEquipment();
    foreach ($cces as $cce) {
      $ccs[] = $cce->getControllerChannel();
    }
    return $ccs;
  }


  /**
   * @param  ControllerChannel the controller channel to add
   */
  public function addControllerChannel(ControllerChannel $cc) {
    $cc->addEquipment($this);
  }

  /**
   * @param  ControllerChannel the controller channel to remove
   */
  public function removeControllerChannel(ControllerChannel $cc) {
    $cc->removeEquipment($this);
  }

  /**
   * Each Equipment is associated with a directory on disk.
   * This function returns the path of that directory for
   * this EquipmentModel.
   *
   * Normally, an equipment is assosiated with a Facility. However, I saw a few
   * organization that is not a Facility so, I use the Name insted of ShortName in those cases
   */
  public function getPathname() {
    $shortname = $this->getOrganization()->getShortName();
    if(empty($shortname)) $shortname = $this->getOrganization()->getName();

    return '/nees/home/facility.groups/' . $shortname . '/Equipment/' . $this->getId();
  }

} // Equipment
?>
