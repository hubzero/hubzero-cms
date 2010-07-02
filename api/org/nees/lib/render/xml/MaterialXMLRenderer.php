<?php
/**
 * @title MaterialXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a Material domain objecti and it's subordinate objects into a
 * chunk of XML
 *
 * @author
 *    Ben Steinberg (with much thanks to Jessica)
 *
 */

require_once "lib/render/Renderer.php";

class MaterialXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
  	if (!$obj instanceof Material) {
      throw new Exception("MaterialArrayRenderer cannot render object of type " . get_class($obj));
    }

    $output = "<material>\n";
    $output .= "\t<id>" . $obj->getId() . "</id>\n";
    $output .= "\t<expid>". $obj->getExperiment()->getId() . "</expid>\n";
    $output .= "\t<name>". $obj->getName() . "</name>\n";
    $output .= "\t<type>". $obj->getMaterialType()->getName() . "</type>\n";
    $output .= "\t<description>". $obj->getDescription() . "</description>\n";

    $props = $obj->getMaterialProperties();
    if( count($props) > 0 ) {
      $output .= "\t<properties>\n";
      $i=1;
      foreach( $props as $prop ) {
        $output .= "\t\t<property";
        $output .= " name=\"".xmlEncode($prop->getMaterialTypeProperty()->getName())."\"";
        $unitObj = $prop->getUnit();
        // if units aren't set in MaterialProperty, use default
        $units = ($unitObj == null) ? $prop->getMaterialTypeProperty()->getUnits() : $unitObj->getAbbreviation();
        $val = $prop->getValue();
        // if no value, why print units
        if ($val == null) $units = null;
        $output .= " units=\"".xmlEncode($units)."\"";
        $output .= ">".$val."</property>\n";
      }

      $output .= "\t</properties>\n";
    }

    $files = $obj->getFiles();
    if( count($files) > 0 ) {
      $output .= "\t<materialfiles>\n";
      foreach( $files as $mfile ) {
        $file = $mfile->getDataFile();
        $output .= "\t\t<file path=\"".$file->get_url()."\">".$file->getName()."</file>\n";
      }
      $output .= "\t</materialfiles>\n";
    }

    $output .= "</material>\n";
    return $output;
  }
}
?>
