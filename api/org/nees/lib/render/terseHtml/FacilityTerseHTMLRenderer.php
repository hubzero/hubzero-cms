<?php
require_once "lib/render/TerseHTMLRenderer.php";

class FacilityTerseHTMLRenderer extends TerseHTMLRenderer {

  function getObjectUrl(BaseObject $obj) {
    return "/?action=DisplayFacility&facid=" . $obj->getId();
  }

  protected function includeMethod(ReflectionMethod $method, ReflectionClass $class) {
    $methodName = $method->getName();
    return (($methodName == "getName") ||
            ($methodName == "getDepartment") ||
            ($methodName == "getLaboratory"));
  }
}
?>