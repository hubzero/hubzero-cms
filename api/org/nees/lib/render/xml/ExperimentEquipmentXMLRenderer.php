<?php
/**
 * @title ExperimentEquipmentXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a ExperimentEquipment domain objecti and it's subordinate objects into a
 * chunk of XML
 *
 * @author
 *    Ben Steinberg (with much thanks to Jessica)
 *
 */

require_once "lib/render/Renderer.php";
require_once "lib/data/ExperimentEquipment.php";

class ExperimentEquipmentXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
    if (!$obj instanceof Experiment) {
      throw new Exception("ExperimentArrayRenderer cannot render object of type " . get_class($obj));
    }

    $equipment = ExperimentEquipmentPeer::findByExperiment($obj->getId());
    $output = "";

    if(count($equipment) > 0) {
      $output = "<experimentequipment>\n";
      $output .= "\t<expid>". $equipment[0]->getExperiment()->getId() . "</expid>\n";
      $output .= "\t<equipment>\n";
      foreach($equipment as $equip) {
        $output .= "\t\t<eeid>" . $equip->getId() . "</eeid>\n";
        $item = $equip->getEquipment();
        $output .= "\t\t<eid>" . $item->getId() . "</eid>\n";
        $output .= "\t\t<name>". $item->getName() . "</name>\n";
      }
      $output .= "\t</equipment>\n";
      $output .= "</experimentequipment>\n";
    }



    return $output;
  }
}
?>
