<?php

/**
 * @title TrialXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a Trial domain object into a
 * chunk of XML compatible with htdocs/api/NEES.xsd's trialMetadataType
 *
 * @author
 *    Jinghong Gao
 *
 */

require_once "lib/render/Renderer.php";

class TrialXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
  	if (!$obj instanceof Trial) {
      throw new Exception("TrialArrayRenderer cannot render object of type " . get_class($obj));
    }

    $experiment = $obj->getExperiment();
    $project = $experiment->getProject();
    $repetitions = $obj->getRepetitions();

    $output = "<trial>\n";
    $output .= "  <id>" . $obj->getId() . "</id>\n";
    $output .= "  <path></path>\n";  // TODO -- experiment's path?
    $output .= "  <name>" . $obj->getName() . "</name>\n";
    $output .= "  <title>" . $obj->getTitle() . "</title>\n";
    $output .= "  <objective>" . $obj->getObjective() ."</objective>\n";
    $output .= "  <description>" . $obj->getDescription() . "</description>\n";
    $output .= "  <start-date>" . $obj->getStartDate() . "</start-date>\n";
    $output .= "  <end-date>" . $obj->getEndDate() . "</end-date>\n";
    $output .= "  <scaling-factor>" . $obj->getAcceleration() . "</scaling-factor>\n";
    $output .= "  <base-acceleration>" . $obj->getBaseAcceleration() . "</base-acceleration>\n";
    $output .= "  <input-motion-name>" . $obj->getMotionName() . "</input-motion-name>\n";

    if (!is_null($repetitions)) {
      foreach ($repetitions as $r) {
        $output .= "  <repetition>https://" . $_SERVER['SERVER_NAME'] . "/repetition/get/" .
          $project->getId() . "/" . $experiment->getId() . "/" . $obj->getId() . "/" . $r->getId() . "/XML</repetition>\n";
      }
    }
    $output .= "</trial>\n";

    return $output;
  }
}
?>
