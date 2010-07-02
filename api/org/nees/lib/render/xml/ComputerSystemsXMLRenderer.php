<?php
/**
 * @title ComputerSystemsXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a ExperimentEquipment domain object and its subordinate objects into a
 * chunk of XML
 *
 * @author
 *    Ben Steinberg (with much thanks to Jessica)
 *
 */

require_once "lib/render/Renderer.php";
require_once "lib/data/ExperimentEquipment.php";
require_once "lib/data/EquipmentAttributeValue.php";

class ComputerSystemsXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
    if (!$obj instanceof Simulation) {
      throw new Exception("ExperimentArrayRenderer cannot render object of type " . get_class($obj));
    }

    $expEquipment = ExperimentEquipmentPeer::findBySimulation($obj->getId());
    $output = "";

    if(count($expEquipment) > 0) {
      $output = "<computersystems>\n";
      $expid= $obj->getId();
      $output .= "\t<expid>". $expid . "</expid>\n";
      foreach($expEquipment as $expEquip) {
        $output .= "\t<equipment>\n";
        $output .= "\t\t<eeid>" . $expEquip->getId() . "</eeid>\n";
        $item = $expEquip->getEquipment();
        $itemId = $item->getId();
        $output .= "\t\t<eid>" . $item->getId() . "</eid>\n";
        $model = $item->getEquipmentModel();
        $eClass = $model->getEquipmentClass();
        $output .= "\t\t<emodel>" . $model->getName() . "</emodel>\n";
        $output .= "\t\t<eclassname>" . $eClass->getClassName() . "</eclassname>\n"  ;
        $output .= "\t\t<eclassid>" . $eClass->getId() . "</eclassid>\n"  ;
        $output .= "\t\t<label>". $item->getName() . "</label>\n";
        $attributeValues = EquipmentAttributeValuePeer::findByEquipment($itemId);
        if (!is_null($attributeValues)) {
          foreach ($attributeValues as $av) {
            $value =	$av->getValue();
            if (($value) && ($value != "")) {
              $output .= "\t\t<attribute>\n";
              $output .= "\t\t\t<name>". $av->getEquipmentAttribute()->getLabel() . "</name>\n";
              $output .= "\t\t\t<value>". $value . "</value>\n";
              $output .= "\t\t</attribute>\n";
            }
          }
        }
        $output .= "\t</equipment>\n";
      }
      $output .= "</computersystems>\n";
    }

    return $output;
  }
}
?>
