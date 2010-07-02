<?php

require_once 'lib/data/om/BaseControllerChannelEquipment.php';


/**
 * ControllerChannelEquipment
 *
 * Relationship representing Equipment used on a given ControllerChannel
 *
 *
 * @package    lib.data
 *
 * @uses ControllerChannel
 * @uses Equipment
 */

class ControllerChannelEquipment extends BaseControllerChannelEquipment {

  public function __construct(ControllerChannel $channel = null,
                              Equipment $equipment = null,
                              $type = null,
                              $description = null) {

    $this->setControllerChannel($channel);
    $this->setEquipment($equipment);
    $this->setType($type);
    $this->setDescription($description);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $ch = $this->getControllerChannel();
    return $ch->getRESTURI() . "/Equipment/{$this->getId()}";
  }


} // ControllerChannelEquipment
?>
