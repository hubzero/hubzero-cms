<?php

/**
 * @title RepetitionXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a Repetition domain object into a
 * chunk of XML compatible with htdocs/api/NEES.xsd's repetitionMetadataType
 *
 * @author
 *    Jinghong Gao
 *
 */

require_once "lib/render/Renderer.php";

class RepetitionXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
    if (!is_a($obj, "Repetition")) {
      throw new Exception("RepetitionArrayRenderer cannot render object of type " . get_class($obj));
    }

    $output = "<repetition>\n";
    $output .= "  <id>" . $obj->getId() . "</id>\n";
    $output .= "  <path></path>\n";  // TODO -- repetition's path?
    $output .= "  <name>" . $obj->getName() . "</name>\n";
    $output .= "  <start-date>" . $obj->getStartDate() . "</start-date>\n";
    $output .= "  <end-date>" . $obj->getEndDate() . "</end-date>\n";
    $output .= "</repetition>\n";

    return $output;
  }
}
?>
