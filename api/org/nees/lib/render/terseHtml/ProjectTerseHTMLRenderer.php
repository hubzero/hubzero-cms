<?php
require_once "lib/render/TerseHTMLRenderer.php";

class ProjectTerseHTMLRenderer extends TerseHTMLRenderer {

  function getObjectUrl(BaseObject $obj) {
    return "/?action=DisplayProjectMain&projid=" . $obj->getId();
  }

  protected function includeMethod(ReflectionMethod $method, ReflectionClass $class) {
    $methodName = $method->getName();
    return ($methodName == "getTitle");
  }
}
?>