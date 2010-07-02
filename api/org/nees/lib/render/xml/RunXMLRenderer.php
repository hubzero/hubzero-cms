<?php

/**
 * @title TrialXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a Trial domain object into a
 * chunk of XML compatible with htdocs/api/NEES.xsd's trialMetadataType
 * run is a special case of Trial for simulations
 * @author
 *    Jinghong Gao
 *
 */

require_once "lib/render/Renderer.php";
require_once "lib/data/SimulationType.php";
require_once "lib/data/ElementType.php";

class RunXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
  	if (!$obj instanceof SimulationRun ) {
      throw new Exception("RunArrayRenderer cannot render object of type " . get_class($obj));
    }
     $simtype = SimulationTypePeer::find($obj->getSimulationType());
     $elemtype = ElementTypePeer::find($obj->getSimulationElementType());

    $output = "<run>\n";
    $output .= "  <id>" . $obj->getId() . "</id>\n";
    $output .= "  <path></path>\n";  // TODO -- simulation's path?
    $output .= "  <name>" . $obj->getName() . "</name>\n";
    $output .= "  <title>" . $obj->getTitle() . "</title>\n";
    $output .= "  <objective>" . $obj->getObjective() ."</objective>\n";
    $output .= "  <description>" . $obj->getDescription() . "</description>\n";
    $output .= "  <start-date>" . $obj->getStartDate() . "</start-date>\n";
    $output .= "  <end-date>" . $obj->getEndDate() . "</end-date>\n";
    $output .= "  <loadtype>" . $obj->getSimulationLoadType() . "</loadtype>\n";
    $output .= "  <elementtype>" . $elemtype->getName() . "</elementtype>\n";
    $output .= "  <simulationtype>" . $simtype->getName() . "</simulationtype>\n";

    if ($obj->hasDataFiles('MainInput'))  {
    	$files = $obj->getMainInputFile();
    	foreach ($files as $file) {
    		$output .= " <maininputfile path=\"". $file->get_url()."\">". $file->getName() ."</maininputfile>\n";
    	}
    }
    if ($obj->hasDataFiles("Input")) {
    	$files = $obj->getInputFiles();
    	foreach ($files as $file) {
    		$output .= " <inputfile path=\"". $file->get_url()."\">". $file->getName() ."</inputfile>\n";
    	}
    }
     if ($obj->hasDataFiles("Output")) {
    	$files = $obj->getOutputFiles();
    	foreach ($files as $file) {
    		$output .= " <outputfile path=\"". $file->get_url()."\">". $file->getName() ."</outputfile>\n";
    	}
    }
    $output .= "</run>\n";

    return $output;
  }
}
?>
