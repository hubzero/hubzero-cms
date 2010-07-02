<?php

/**
 * @title TrialArrayRenderer
 *
 * @abstract
 *    A class that knows how to convert a Trial domain object into an
 * associative array with keys that are expected by "the old code"
 *
 * @author
 *    Adam Birnbaum
 *
 */

require_once "lib/render/Renderer.php";

class RepetitionArrayRenderer implements Renderer {
  function render( BaseObject $obj , $title = null) {
    if (!is_a($obj, "Repetition")) {
      throw new Exception("RepetitionArrayRenderer cannot render object of type " . get_class($obj));
    }

    $array["repid"] = $obj->getId();
    $array["Name"] = $obj->getName();
    $array["repid"] = $obj->getId();
    $array["Status"] = $obj->getStatus();
    $array["StartDate"] = $obj->getStartDate();
    $array["EndDate"] = $obj->getEndDate();
    $array["trialid"] = $obj->getTrial()->getId();

    return $array;
  }
}


?>
