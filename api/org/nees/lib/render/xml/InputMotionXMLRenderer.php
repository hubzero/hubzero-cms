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

class InputMotionXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
    if (!$obj instanceof Trial) {
      throw new Exception("TrialArrayRenderer cannot render object of type " . get_class($obj));
    }


    $output  = "<inputmotion>\n";
    $output .= "\t<trialid name=\"".$obj->getName()."\" title=\"".$obj->getTitle()."\">" . $obj->getId() . "</trialid>\n";
    $output .= "\t<input-motion-name>" . $obj->getMotionName() . "</input-motion-name>\n";
    $output .= "\t<acceleration>" . $obj->getAcceleration() . "</acceleration>\n";
    $output .= "\t<station>" . $obj->getStation() . "</station>\n";
    $output .= "\t<component>" . $obj->getComponent() . "</component>\n";
    $output .= "\t<base-acceleration>" . $obj->getBaseAcceleration() . "</base-acceleration>\n";
    if( $obj->getMotionFile() ) {
      $dfile = $obj->getMotionFile();
      $output .= "\t\t<motion-file path=\"".$dfile->get_url()."\">".$dfile->getName()."</motion-file>\n";
    }
    $output .= "</inputmotion>\n";

    return $output;
  }
}
?>
