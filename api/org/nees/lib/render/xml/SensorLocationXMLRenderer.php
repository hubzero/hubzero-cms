<?php
/**
 * @title SensorLocationPlanXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a SensorLocationPlan domain objecti and it's subordinate objects into a
 * chunk of XML
 *
 * @author
 *    Ben Steinberg (with much thanks to Jessica)
 *
 */


require_once "lib/render/Renderer.php";

class SensorLocationPlanXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
  	if (!$obj instanceof SensorLocationPlan) {
      throw new Exception("SensorLocationPlanArrayRenderer cannot render object of type " . get_class($obj));
    }

    $output = "<sensorlocationplan>\n";
    $output .= "\t<id>" . $obj->getId() . "</id>\n";
    $output .= "\t<expid>". $obj->getExperiment()->getId() . "</expid>\n";
    $output .= "\t<name>". $obj->getName() . "</name>\n";
    foreach ($obj->getSensorLocations() as $sl) {
      $output .= "\t<sensorlocations>\n";

      //$output .= "\t\t<sensortype>". SensorType::getInstance()->disp($sl->getSensorType())."</sensortype>\n";
      $st = $sl->getSensorType();
      $output .= "\t\t<sensortype>".$st->getName()."</sensortype>\n";
      $output .= "\t\t<sensortypeid>".$st->getId()."</sensortypeid>\n";
      $label = is_null($sl->getLabel())? "" : $sl->getLabel();
      $output .= "\t\t<sensorlabel>".  $label ."</sensorlabel>\n";
      $output .= "\t\t<sensorX>". $sl->getX()  ."</sensorX>\n";
      $output .= "\t\t<sensorY>". $sl->getY()  ."</sensorY>\n";
      $output .= "\t\t<sensorZ>". $sl->getZ()  ."</sensorZ>\n";
      $output .= "\t\t<sensorI>". $sl->getI()  ."</sensorI>\n";
      $output .= "\t\t<sensorJ>". $sl->getJ()  ."</sensorJ>\n";
      $output .= "\t\t<sensorK>". $sl->getK()  ."</sensorK>\n";

      $cs = $sl->getCoordinateSpace();
      if (!is_null($cs)) {
        $output .= "\t\t<coordinatespacename>".$cs->getName()."</coordinatespacename>\n";
        $output .= "\t\t<coordinatespaceid>".$cs->getId()."</coordinatespaceid>\n";
      }
      else {
        $output .= "\t\t<coordinatespacename></coordinatespacename>\n";
        $output .= "\t\t<coordinatespaceid></coordinatespaceid>\n";
      }
      $output .= "\t</sensorlocations>\n";
    }
    $output .= "</sensorlocationplan>\n";
    return $output;
  }
}
?>
