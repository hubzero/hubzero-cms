<?php

require_once 'lib/data/om/BaseControllerChannel.php';
require_once 'lib/data/Location.php';

/**
 * ControllerChannel
 *
 * An analogue of DAQChannel specific to the controller side of the experiment.
 * May be redundant.
 *
 * ControllerChannels (or, just, "Channels") are
 * channels from a given experimental Trial
 *
 *
 * @abstract
 *
 * @package    lib.data
 * @uses DataFile
 * @uses ControllerConfig
 * @uses Sensor
 * @uses SourceLocation
 * @uses Equipment
 *
 * @todo explain this better
 */

class ControllerChannel extends BaseControllerChannel {

  /**
   * Initializes internal state of ControllerChannel object.
   */
  public function __construct(ControllerConfig $controllerconf = null,
                              $name = "",
                              $description = null,
                              $station = null,
                              $direction = null,
                              Equipment $equipment = null,
                              SourceLocation $sourceLocation = null,
                              DataFile $datafile = null) {

    $this->setControllerConfig($controllerconf);
    $this->setName($name);
    $this->setDescription($description);
    $this->setStation($station);
    $this->setDirection($direction);
    $this->SetEquipment($equipment);
    $this->setSourceLocation($sourceLocation);
    $this->setDataFile($datafile);
  }

  /**
   * Get the Equipment
   * Backward compatible NEEScentral 1.7
   *
   * @return Equipment
   */
  public function getSource() {
    return BaseControllerChannel::getEquipment();
  }


  /**
   * Set the Equipment for this ControllerChannel
   * Backward compatible NEEScentral 1.7
   *
   * @param Equipment $equipment
   */
  public function setSource(Equipment $equipment) {
    return self::setEquipment($equipment);
  }


  /**
   * Set SourceLocation for this Channel
   *
   * @param SourceLocation $sourceLocation
   */
  public function setSourceLocation($sourceLocation) {
    if(is_null($sourceLocation)) {
      $this->setSourceLocationId(null);
    }
    else {
      $this->setSourceLocationId($sourceLocation->getId());
    }
  }

  /**
   * Get the SourceLocation defined by this channel
   *
   * @return SourceLocation Object
   */
  public function getSourceLocation() {
    $id = $this->getSourceLocationId();
    if(is_null($id)) return null;
    else return LocationPeer::findSourceLocationById($id);
  }


  /**
   * Get the list of ControllerChannelEquipments
   * Backward compatible NEEScentral 1.7
   *
   * @return array <ControllerChannelEquipment>
   */
  public function getControllerChannelEquipment() {
    return $this->getControllerChannelEquipments();
  }




  /**
   * Each channel has an index number, this method
   * iterates through the parent {@link ControllerConfig} of
   * this channel and finds the index of this ControllerChannel
   *
   * @return integer index number of this channel w/in parent config
   */
  public function getChannelIndex() {
    $config = $this->getControllerConfig();
    $channels = $config->getChannels();

    $i = 0;
    $thisId = $this->getId();
    foreach ($channels as $c) {
      if ($c->getId() == $thisId) {
        return $i;
      }
      $i++;
    }

    throw new Exception("Channel was not in the DAQ Configuration.");
  }

  /**
   * Get the input file associated with this channel
   * This is the input file associate with the parent {@link ControllerConfig}
   *
   * @return DataFile input data file of parent ControllerConfig
   */
  public function getDataFiles() {
    $config = $this->getControllerConfig();
    $dir = $config->getInputDataFile();
    if(!is_null($dir)) {
      return $dir->getFilesInDir(false);
    }
    return;
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $cl = $this->getControllerConfig();
    return $cl->getRESTURI() . "/ControllerChannel/{$this->getId()}";
  }


   /**
    * Backward compatible with NEEScentral 1.7
    */
  public function setChannelEquipment($equipments) {
    if(is_null($equipments)) $equipments = array();

    $this->collControllerChannelEquipments = $equipments;
  }


  /**
   * Get the list of ControllerChannelEquipments
   *
   * @return array <ControllerChannelEquipments>
   */
  public function getChannelEquipment() {
    return $this->getControllerChannelEquipmentsJoinEquipment();
  }



  /**
   * @param Equipment the equipment to add
   */
  public function addChannelEquipment(Equipment $e) {
    if ($this->getPeer()->countControllerChannelEquipment($this, $e) == 0) {
      $cce = new ControllerChannelEquipment();
      $cce->setEquipment($e);
      $cce->setControllerChannel($this);
      $cce->save();
    }
  }

  /**
   * @param Equipment the equipment to remove
   */
  public function removeChannelEquipment(Equipment $e) {
    $cces = $this->getPeer()->findControllerChannelEquipment($this,$e);
    foreach ($cces as $cce) {
      $cce->delete();
    }
  }



} // ControllerChannel
?>
