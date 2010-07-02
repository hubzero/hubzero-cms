<?php
/**
 * @title CoordinateXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a Coordinate domain objecti and it's subordinate objects into a
 * chunk of XML
 *
 * @author
 *    Ben Steinberg (with much thanks to Jessica)
 *
 */

require_once "lib/render/Renderer.php";

class CoordinateXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
    if (!$obj instanceof CoordinateSpace) {
      throw new Exception("CoordinateSpaceArrayRenderer cannot render object of type " . get_class($obj));
    }

    $scale = $obj->getScale();
    $trans = $obj->getTranslation();
    $rot   = $obj->getRotation();
    $transUnits = $obj->getTranslationUnits();
    $rotUnits   = $obj->getRotationUnits();
    if( $obj->getParentSpace() ) {
      $parentName = $obj->getParentSpace()->getName();
    }
    else $parentName = '';
    $validDate = '';
    if ($obj->getDateCreated()){
      $validDate = date( 'm/d/Y h:i a', strtotime($obj->getDateCreated()) ) ;
    }


    $output = "<coordinatespace>\n";
    $output .= "\t<id>" . $obj->getId() . "</id>\n";
    $output .= "\t<expid>". $obj->getExperiment()->getId() . "</expid>\n";
    $output .= "\t<name>". xmlEncode($obj->getName()) . "</name>\n";
    $output .= "\t<description>". xmlEncode($obj->getDescription()) . "</description>\n";
    $output .= "\t<timestamp>". $validDate . "</timestamp>\n";
    $output .= "\t<parent>" . $parentName . "</parent>\n";
    $output .= "\t<scale>".$scale."</scale>\n";
    $output .= "\t<system>".$obj->getSystem()->getName(). "</system>\n";
    $output .= "\t<translation>\n";
    $output .= "\t\t<dimension name=\"".xmlEncode($obj->getSystem()->getCoordinateDimensionRelatedByDimension1()->getName())."\" units=\"".$transUnits[0]->getName()."\">". $trans[0]  . "</dimension>\n";
    $output .= "\t\t<dimension name=\"".xmlEncode($obj->getSystem()->getCoordinateDimensionRelatedByDimension2()->getName())."\" units=\"".$transUnits[1]->getName()."\">". $trans[1]  . "</dimension>\n";
    $output .= "\t\t<dimension name=\"".xmlEncode($obj->getSystem()->getCoordinateDimensionRelatedByDimension3()->getName())."\" units=\"".$transUnits[2]->getName()."\">". $trans[2]  . "</dimension>\n";
    $output .= "\t</translation>\n";

    $output .= "\t<rotation>\n";
    $output .= "\t\t<dimension name=\"".xmlEncode("&theta;")."1\" units=\"".xmlEncode($rotUnits[0]->getName())."\">". $rot[0] . "</dimension>\n";
    $output .= "\t\t<dimension name=\"".xmlEncode("&theta;")."2\" units=\"".xmlEncode($rotUnits[1]->getName())."\">". $rot[1] . "</dimension>\n";
    $output .= "\t\t<dimension name=\"".xmlEncode("&theta;")."3\" units=\"".xmlEncode($rotUnits[2]->getName())."\">". $rot[2] . "</dimension>\n";
    // $output .= "\t\t<dimension name=\"theta3\" units=\"".xmlEncode($rotUnits[2]->getName())."\">". $rot[2] . "</dimension>\n";
    $output .= "\t</rotation>\n";

    $files = $obj->getDataFiles();

    if( count($files) > 0 ) {
      $output .= "\t<coordinatefiles>\n";
      foreach( $files as $mfile ) {
        $file = $mfile->getDataFile();
        $output .= "\t\t<file path=\"".$file->get_url()."\">".$file->getName()."</file>\n";
      }
      $output .= "\t</coordinatefiles>\n";
    }

    $output .= "</coordinatespace>\n";
    return $output;
  }
}
?>
