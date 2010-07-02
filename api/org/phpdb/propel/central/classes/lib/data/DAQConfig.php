<?php

require_once 'lib/data/om/BaseDAQConfig.php';


/**
 * DAQChannel (aka ChannelList)
 *
 * Represents the set of [DAQ]Channels used in a particular trial
 *
 * @package    lib.data
 *
 * @uses Trial
 * @uses DAQChannel
 * @uses DataFile
 * @uses Equipment
 *
 * @todo implement clone if the propel version doesn't work good
 */

class DAQConfig extends BaseDAQConfig {

  /**
   * Initializes internal state of DAQConfig object.
   */
  public function __construct(Trial $trial = null,
                              $name="",
                              $description = null,
                              Equipment $daq_equipment = null,
                              DataFile $configdatafile = null,
                              DataFile $outputdatafile = null ) {
    $this->setTrial($trial);
    $this->setName($name);
    $this->setDescription($description);
    $this->setEquipment($daq_equipment);
    $this->setDataFileRelatedByConfigDataFileId($configdatafile);
    $this->setDataFileRelatedByOutputDataFileId($outputdatafile);
  }

  /**
   * Wrap (@link BaseDAQConfig::addDAQChannel}
   */
  public function addChannel(DAQChannel $daq_channel) {
    $this->addDAQChannel($daq_channel);
  }


  public function removeChannel(DAQChannel $channel) {
    if(is_null($channel)) return;

    if(in_array($channel, $this->getDAQChannels())) {
      $channel->delete();
    }
  }

  /**
   * Wrap {@link BaseDAQConfig::getDAQChannels} to support existing interface
   *
   * @deprecated
   * @return array<DAQChannel> array of DAQChannels
   */
  public function getChannels() {
    return $this->getDAQChannels();
  }

  /**
   * Wrap {@link BaseDAQConfig::addDAQChannels} to support existing interface
   *
   * @deprecated
   * @param array<DAQChannel> array of DAQChannels
   */
  public function setChannels($coll) {

    if(is_null($coll)) $coll = array();
    $this->collDAQChannels = $coll;
  }

  /**
   * Wrap (@link BaseDAQConfig::getDataFileRelatedByConfigDataFileId }
   * Backward compatible to NEEScentral 1.7
   *
   * @return DataFile the Configuration DataFile
   */
  public function getConfigDataFile() {
    return $this->getDataFileRelatedByConfigDataFileId();
  }

   /**
   * Wrap (@link BaseDAQConfig::getDataFileRelatedByConfigDataFileId }
   * Backward compatible to NEEScentral 1.7
   *
   * @param DataFile the Configuration DataFile
   */
  public function setConfigDataFile($datafile) {
    $this->setDataFileRelatedByConfigDataFileId($datafile);
  }

  /**
   * Wrap (@link BaseDAQConfig::getDataFileRelatedByOutputDataFileId }
   * Backward compatible to NEEScentral 1.7
   *
   * @return DataFile the Output DataFile
   */
  public function getOutputDataFile() {
    return $this->getDataFileRelatedByOutputDataFileId();
  }

   /**
   * Wrap (@link BaseDAQConfig::getDataFileRelatedByConfigDataFileId }
   * Backward compatible to NEEScentral 1.7
   *
   * @param DataFile the Configuration DataFile
   */
  public function setOutputDataFile($datafile) {
    $this->setDataFileRelatedByOutputDataFileId($datafile);
  }

  /**
   * Wrap (@link BaseDAQConfig::getEquipment}
   * Backward compatible to NEEScentral 1.7
   *
   * @return Equipment the DAQ Equpment
   * @deprecated
   */
  public function getDAQ() {
    return $this->getEquipment();
  }

    /**
   * Wrap (@link BaseDAQConfig::getEquipment}
   * Backward compatible to NEEScentral 1.7
   *
   * @param Equipment the DAQ Equpment
   * @deprecated
   */
  public function setDAQ($daq) {
    $this->setEquipment($daq);
  }


  /**
   * canExportToDataViewer
   *
   * @param string $viewer the viewer for which to validate
   * @return bool   true if this DAQConfig can be exported to the given viewer
   *
   * @todo enumerate the list of viewers...
   */
  public function canExportToDataViewer($viewer) {

    switch ($viewer) {

      case "n3dv":
        if( $this->hasDAQChannels() && $this->hasOutputFile() ) {
          return true;
        }
        return false;

      default:
        return false;

    }
  }


  /**
   * Check if this DAQConfig has DAQChannels or not
   *
   * @return boolean value
   */
  public function hasDAQChannels() {
    return (($channels = $this->getDAQChannels()) && count($channels) > 0 );
  }


  /**
   * Check if this DAQConfig has Output Files or not
   *
   * @return boolean value
   */
  public function hasOutputFile() {
    return ! is_null($this->getOutputDataFile());
  }



  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $trial = $this->getTrial();
    return $trial->getRESTURI() . "/DAQConfig/{$this->getId()}";
  }

} // DAQConfig
?>
