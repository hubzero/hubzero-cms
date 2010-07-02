<?php

require_once 'lib/data/om/BaseControllerConfig.php';


/**
 * ControllerConfig (aka ChannelList)
 *
 * Represents the set of Channels used in a particular Trial
 *
 * A source is any kind of instrument that induces a load into the system.
 * A controllerCOnfig aggregates the configuration of the chann
 *
 * Trying to get what the input motion is... upload files that describe the
 * input motion of the source/loading protocol;. also the source might
 * have sensors of their own attached; most actuators comes with a
 *  built-in load-cell, for instance; need to record that reading.
 *
 * The difference between Source and DAQ is an implementation difference
 * not a conceptual difference.
 *
 * Basically a description of the control system for a given test. Included
 * in this are the inputs into the control system.
 *
 * Separate source channel list is confusing/incorrect.
 *
 * @todo rename to something more useful like SourceControllerConfig
 *
 * @package    lib.data
 * @uses DataFile
 * @uses MeasurementUnit
 * @uses Trial
 * @uses Equipment
 * @uses ControllerChannel
 */
class ControllerConfig extends BaseControllerConfig {

  /**
   * Initialize a ControllerConfig Object
   *
   * @param Trial $trial
   * @param string $name
   * @param string $description
   * @param Equipment $equipment
   * @param DataFile $config_datafile
   * @param DataFile $input_datafile
   */
  public function __construct(Trial $trial = null,
                              $name="",
                              $description = null,
                              Equipment $equipment = null,
                              DataFile $config_datafile = null,
                              DataFile $input_datafile = null )
  {
    $this->setTrial($trial);
    $this->setName($name);
    $this->setDescription($description);
    $this->setEquipment($equipment);
    $this->setDataFileRelatedByConfigDataFileId($config_datafile);
    $this->setDataFileRelatedByInputDataFileId($input_datafile);
  }


  /**
   * get Config dataFile for this Controller
   * backward compatible to NEEScentral 1.7
   *
   * @return DataFile
   */
  public function getConfigDataFile() {
    return $this->getDataFileRelatedByConfigDataFileId();
  }


  /**
   * Set ConfigDataFile for this controller,
   * backward compatible to NEEScentral 1.7
   *
   * @param DataFile $config_datafile
   */
  public function setConfigDataFile($config_datafile) {
    if(is_null($config_datafile)) return;
    return $this->setDataFileRelatedByConfigDataFileId($config_datafile);
  }


  /**
   * get Input dataFile for this Controller
   *
   * @return DataFile
   */
  public function getInputDataFile() {
    return $this->getDataFileRelatedByInputDataFileId();
  }



  /**
   * Set InputDataFile for this controller
   * backward compatible to NEEScentral 1.7
   *
   * @param DataFile $input_datafile
   */
  public function setInputDataFile($input_datafile) {
    if(is_null($input_datafile)) return;
    return $this->setDataFileRelatedByInputDataFileId($input_datafile);
  }


  /**
   * Wrap the {@link BaseControllerConfig::getControllerChannels method for
   * interface compatibility
   *
   * @return array List of ControllerChannels in this configuration
   * @deprecated Use getControllerChannels
   */
  public function getChannels() {
    return $this->getControllerChannels();
  }


  /**
   * Wrap the {@link BaseControllerConfig::getControllerChannels} method
   *  for interface compatibility
   *
   * @return array List of ControllerChannels in this configuration
   * @deprecated
   */
  public function addChannel(ControllerChannel $channel) {
    return $this->addControllerChannel($channel);
  }

  /**
   * Remove a channel from the ControllerConfig
   *
   * @todo $channel: the ControllerChannel to be removed
   */
  public function removeChannel(ControllerChannel $channel) {
    if(is_null($channel)) return;

    if(in_array($channel, $this->collControllerChannels)) $channel->delete();

    $newColl = array();
    foreach($this->collControllerChannels as $c) {

      // If the item is not the one to be deleted then push it back to the new array
      if($c->getId() != $channel->getId()) {
        $newColl[] = $c;
      }
    }

    // Set the collControllerChannels to the new list
    $this->collControllerChannels = $newColl;
  }


  /**
   * Wrap the {@link BaseControllerConfig::getEquipment} method for
   * interface compatibility
   *
   * @return ControllerConfig
   * @deprecated
   */
  public function getController() {
    return $this->getEquipment();
  }

  /**
   * Wrap the {@link BaseControllerConfig::setEquipment} method for
   * interface compatibility
   *
   * @return ControllerConfig
   * @deprecated
   */
  public function setController($equipment) {
    return $this->setEquipment($equipment);
  }

} // ControllerConfig
?>
