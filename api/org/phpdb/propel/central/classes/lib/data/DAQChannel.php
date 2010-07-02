<?php

require_once 'lib/data/om/BaseDAQChannel.php';
require_once 'lib/data/Location.php';


/**
 * DAQChannel
 *
 * Represents a data-acquisition (DAQ) channel in a
 * given experimental {@link Trial} which consists of
 * an output from a sensor or a control-system specific
 * sensor (e.g., a load cell in an actuator)
 *
 * @package    lib.data
 * @uses DAQConfig
 * @uses DataFile
 * @uses SensorLocation
 * @uses Sensor
 * @uses DAQChannelEquipment
 *
 */
class DAQChannel extends BaseDAQChannel {

  /**
   * Initializes internal state of DAQChannel object.
   */
  public function __construct( DAQConfig $daqconfig = null,
                               $name = "",
                               $description = null,
                               $gain = null,
                               $adcrange = null,
                               $adcresolution = null,
                               $excitation = null,
                               Sensor $sensor = null,
                               SensorLocation $sensorLocation = null,
                               DataFile $datafile = null,
                               $channelOrder = null)
  {
    $this->setDAQConfig($daqconfig);
    $this->setName($name);
    $this->setDescription($description);
    $this->setADCRange($adcrange);
    $this->setADCResolution($adcresolution);
    $this->setExcitation($excitation);
    $this->setGain($gain);
    $this->setSensor($sensor);
    $this->setLocation($sensorLocation);
    $this->setDataFile($datafile);
    $this->setChannelOrder($channelOrder);
  }


/*

ChannelNumber == ChannelOrder
SensorLocationPlan ---
SensorLocation ---
Gain ---
ADCRange ---
ADCResolution ---
Excitation ---
Description ---

*/



  /**
   * Get the list of ControllerChannelEquipments
   *
   * @return array <ControllerChannelEquipments>
   */
  public function getChannelEquipment() {
    return $this->getDAQChannelEquipments();
  }


   /**
    * Backward compatible with NEEScentral 1.7
    */
  public function setChannelEquipment($equipments) {
    if(is_null($equipments)) $equipments = array();

    $this->collDAQChannelEquipments = $equipments;
  }


  /**
   * Set SensorLocation for this Channel
   *
   * @param SensorLocation $sensorLocation
   */
  public function setSensorLocation($sensorLocation) {

    $this->setLocation($sensorLocation);

  }

  /**
   * Get the SensorLocation defined by this channel
   *
   * @return SensorLocation
   */
  public function getSensorLocation() {
    return $this->getLocation();
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $cl = $this->getDAQConfig();
    return $cl->getRESTURI() . "/DAQChannel/{$this->getId()}";
  }

  /**
   * Return an array of {@link DataFile} associated with
   * this DAQChannel
   *
   * @return array<DataFile>
   * @deprecated
   */
  public function getOutputDataFiles() {

    $config = $this->getDAQConfig();
    $dir = $config->getOutputDataFile();
    $files = array();

    if( !is_null($dir) ) {
      if ( $dir->getDirectory() ) {
        return $dir->getFilesInDir(false);
      } else {
        $files->add($dir);
      }
    }
    return $files;

  }

  /**
   * Return the index of this DAQChannel within its parent {@link DAQConfig}
   *
   * @return integer
   * @deprecated
   */
  public function getChannelIndex() {
    $config = $this->getDAQConfig();
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
   *
   *
   * @return array<Calibration> Calibrations
   * @deprecated
   * @todo document this
   */
  function getEngineeringUnits() {
    $sensor = $this->getSensor();
    if (!$sensor) {
      return null;
    }

    $calibrations = $sensor->getCalibrations();
  }


} // DAQChannel
?>
