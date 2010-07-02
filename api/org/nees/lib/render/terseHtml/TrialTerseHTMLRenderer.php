<?php
require_once "lib/render/TerseHTMLRenderer.php";

class TrialTerseHTMLRenderer extends TerseHTMLRenderer {

  function getObjectUrl(BaseObject $obj) {
    $exp = $obj->getExperiment();
    return "/?trialid=" . $obj->getId() . "&expid=" . $exp->getId() . "&projid=" . $exp->getProject()->getId() . "&action=DisplayTrialMain";
  }

  protected function includeMethod(ReflectionMethod $method, ReflectionClass $class) {
    $methodName = $method->getName();
    return ($methodName == "getTitle");
  }

 protected function renderMethodCalls(BaseObject $obj) {
    $exp = $obj->getExperiment();
    $retval = $this->renderField("Project", $exp->getProject()->getTitle());
    $retval .= $this->renderField("Experiment Title", $exp->getTitle());
    $retval .= $this->renderField("Trial Title", $obj->getTitle());
    return $retval;
  }

}
?>