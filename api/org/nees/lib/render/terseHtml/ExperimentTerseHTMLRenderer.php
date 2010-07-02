<?php
require_once "lib/render/TerseHTMLRenderer.php";

class ExperimentTerseHTMLRenderer extends TerseHTMLRenderer {

  function getObjectUrl(BaseObject $obj) {
    return "/?action=DisplayExperimentMain&expid=" . $obj->getId() . "&projid=" . $obj->getProject()->getId();
  }

  protected function renderMethodCalls(BaseObject $obj) {
    $retval = $this->renderField("Project", $obj->getProject()->getTitle());
    $retval .= $this->renderField("Experiment Title", $obj->getTitle());
    return $retval;
  }

}
?>