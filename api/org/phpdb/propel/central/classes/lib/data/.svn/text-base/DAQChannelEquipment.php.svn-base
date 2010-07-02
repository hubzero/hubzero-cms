<?php

require_once 'lib/data/om/BaseDAQChannelEquipment.php';

/**
 * DAQChannelEquipment
 *
 * Relates a {@link DAQChannel} to a piece of {@link Equipment}
 * allows for type and description strings
 *
 *
 * @package    lib.data
 *
 * @uses DAQChannel
 * @uses Equipment
 *
 */
class DAQChannelEquipment extends BaseDAQChannelEquipment {

  /**
   * Initializes internal state of DAQChannelEquipment object.
   */
  public function __construct( DAQChannel $channel = null,
                               Equipment $equipment = null,
                               $type = null,
                               $description = null)
  {
    $this->setDAQChannel($channel);
    $this->setEquipment($equipment);
    $this->setType($type);
    $this->setDescription($description);
  }


  /**
   * Return REST URI for this DAQChannelEquipment.
   *
   * DAQChannelEquipment is represented as an <Equipment> element
   * nested in a <DAQChannel> element.
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $ch = $this->getDAQChannel();
    return $ch->getRESTURI() . "/Equipment/{$this->getId()}";
  }


} // DAQChannelEquipment
?>
