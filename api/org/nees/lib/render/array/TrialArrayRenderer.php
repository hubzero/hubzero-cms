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

class TrialArrayRenderer implements Renderer {
  function render( BaseObject $obj , $title = null) {
    if (!is_a($obj, "Trial")) {
      throw new Exception("TrialArrayRenderer cannot render object of type " . get_class($obj));
    }

    $array["trialid"] = $obj->getId();
    $array["Name"] = $obj->getName();
    $array["Title"] = $obj->getTitle();
    $array["Objective"] = $obj->getObjective();
    $array["Description"] = $obj->getDescription();
    $array["StartDate"] = $obj->getStartDate();
    $array["EndDate"] = $obj->getEndDate();
    $array["Status"] = $obj->getStatus();
    $array["Acceleration"] = $obj->getAcceleration();
    $array["BaseAcceleration"] = $obj->getBaseAcceleration();
    $array["BaseAccelerationUnit_id"] = null;
    if( $obj->getBaseAccelerationUnit() ) {
      $array["BaseAccelerationUnit_id"] = $obj->getBaseAccelerationUnit()->getId();
    }
    $array["MotionName"] = $obj->getMotionName();
    $array["Station"] = $obj->getStation();
    $array["Component"] = $obj->getComponent();
    $array["MotionFile"] = $obj->getMotionFile();
    $array["expid"] = $obj->getExperiment()->getId();
    $array["Repetitions"] = count($obj->getRepetitions());

    return $array;
  }
}


?>
