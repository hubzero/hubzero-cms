<?php

/**
 * @title ExperimentArrayRenderer
 *
 * @abstract
 *    A class that knows how to convert a Experiment domain object into an
 * associative array with keys that are expected by "the old code"
 *
 * @author
 *    Adam Birnbaum
 *
 */

require_once "lib/render/Renderer.php";

class ExperimentArrayRenderer implements Renderer {
  function render( BaseObject $obj , $title = null) {
    if (!is_a($obj, "Experiment")) {
      throw new Exception("ExperimentArrayRenderer cannot render object of type " . get_class($obj));
    }

    $array["expid"] = $obj->getId();
    $array["Name"] = $obj->getName();
    $array["Title"] = $obj->getTitle();
    $array["Objective"] = $obj->getObjective();
    $array["Description"] = $obj->getDescription();
    $array["StartDate"] = $obj->getStartDate();
    $array["EndDate"] = $obj->getEndDate();
    $array["Status"] = $obj->getStatus();
    $array["projid"] = $obj->getProject()->getId();
    $array["structure"] = $obj->getStructure();

    return $array;
  }
}


?>
