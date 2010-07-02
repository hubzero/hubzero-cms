<?php
/**
 * @title SensorLocationPlanXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a SensorLocationPlan domain objecti and it's subordinate objects into a
 * chunk of XML
 *
 * @author
 *    Ben Steinberg (with much thanks to Jessica)
 *
 */


require_once "lib/render/Renderer.php";

class DAQChannelXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
  	if (!$obj instanceof DAQConfig) {
      throw new Exception("DAQConfigArrayRenderer cannot render object of type " . get_class($obj));
    }
    $trial =  $obj->getTrial();
    $exp = $trial->getExperiment();

    $output = "<daqconfig id=\"".$obj->getId()."\">\n";
    $output .= "\t<trialid trialName=\"".$trial->getName()."\">" . $trial->getId() . "</trialid>\n";
    $output .= "\t<expid experimentName=\"".$exp->getName()."\">". $exp->getId() . "</expid>\n";
    $output .= "\t<name>".$obj->getName()."</name>\n";
    $output .= "\t<description>".$obj->getDescription()."</description>\n";

    // single config file for all channels
    $cdf = $obj->getConfigDataFile();
    $daq = $obj->getDAQ();
	if ($cdf) {
      $output .= "\t<ConfigDataFile path=\"".$cdf->get_url()."\">".$cdf->getName()."</ConfigDataFile>\n";
	}
    $odf = $obj->getOutputDataFile();
    if ($odf) {
      $output .= "\t<OutputDataFile path=\"".$odf->get_url()."\">".$odf->getName()."</OutputDataFile>\n";
    }


    $channels = $obj->getChannels();
    foreach ($channels as $channel) {
      $output .= "\t<channel id=\"".$channel->getId()."\">\n";
      $sl = $channel->getSensorLocation();
      $stype = ($sl) ? $sl->getSensorType() : null;
      $type =  ($stype)? $stype->getName() : "";
      $slabel = ($sl) ?  $sl->getLabel() :$channel->getName();

      $output .= "\t\t<type>" . $type . "</type>\n";
      $output .= "\t\t<label>" . $slabel . "</label>\n";
      // do a little temporary quality control - if gain, ADC Resolution or Range or Excitation is 0, should be null
      $myGain =     $channel->getGain();
      // gain is unitless - a multiplicative factor
      if (($myGain) && ($myGain != 0)) {
     	 $output .= "\t\t<gain>" . $myGain . "</gain>\n";
      }
      $myRange = $channel->getADCRange();
      if (($myRange) && ($myRange != 0)) {
      	$units = "V"; //set units to V - not entered or documented anywhere I can find, but standard units for DAQs
      	$output .= "\t\t<ADCRange units=\"".$units."\">" . $myRange . "</ADCRange>\n";
      }
      $myRes = $channel->getADCResolution();
      // units??? mV? microV? V?
      if (($myRes) && ($myRes != 0)) {
      	$output .= "\t\t<ADCResolution>" .$myRes . "</ADCResolution>\n";
      }
      // units for Excitation??? amperes? volts?
      $myExcit = $channel->getExcitation() ;
      if (($myExcit) && ($myExcit != 0)) {
     	 $output .= "\t\t<Excitation>" . $myExcit . "</Excitation>\n";
      }
      // config file for each channel, if present
      $df = $channel->getDataFile();
      if ($df) {
        $output .= "\t\t<datafile path=\"".$df->get_url()."\">".$df->getName()."</datafile>\n";
      }

      $ces = $channel->getChannelEquipment();

      if ($ces) {
      foreach ($ces as $ce) {
        $output .= "\t\t\t<channelequipment id=\"".$ce->getId()."\">\n";
        $output .= "\t\t\t<type>". $ce->getType() ."</type>\n";
        $output .= "\t\t\t<description>". $ce->getDescription() ."</description>\n";
//        $equip = $ce->getEquipment();
//        if (!is_null($equip)) {
        $output .= "\t\t\t</channelequipment>\n";
      }
      }


    $output .= "\t</channel>\n";
    }

  // remove this code for generating DAQ calibration tables temporarily until individual sensors
//  are again added to the DAQchannel table
/*
    foreach ($channels as $channel) {

      $sensor = $channel->getSensor();
      if ($sensor) {
      	$calibs = $sensor->getCalibrations();
      	// don't even include this element if no calibrations tied to the sensor
     	if ($calibs) {
     		$output .= "\t<ChannelCalibrations>\n";
      		$sl = $channel->getSensorLocation();
      		// older entries put labels on channels associated with particular sensors (e.g. accelerometer with serial number 1234 in the sensor table)
      		// current use puts labels on sensor types associated with a particular sensor location (e.g. accelerometer at x,y,z)
      		$slabel = ($sl) ?  $sl->getLabel() :$channel->getName();

      		$output .= "\t\t<label>" . $slabel . "</label>\n";
   			$output .= "\t\t<serialNumber>".$sensor->getSerialNumber()."</serialNumber>";
   			$output .= "\t\t<model>" . $sensor->getSensorModel()->getModel() . "</model>\n";
      		$output .= "\t\t<manufacturer>" . $sensor->getSensorModel()->getManufacturer() . "</manufacturer>\n";

      		// Calibrations are not currently accessible from the web site.
      		// Particular sensors (with serial nos. and calibration histories) are in the sensor table
      		// and are associated with a given facility through its equipment list.
      		// Particular sensors are associated with a given trial through the DAQChannel table,
      		// but the sensor field in DAQChannel is not currently accessible from the web site.

  	     	foreach ($calibs as $calib) {
  	     		$output .= "\t<calibration>";
   				$output .= "\t\t<date>".$calib->getCalibDate()."</date>";
   				$output .= "\t\t<sensitivity units=\"".xmlEncode($calib->getSensitivityUnits())."\">".$calib->getSensitivity()."</sensitivity>";
   				$output .= "\t\t<calibFactor units=\"".xmlEncode($calib->getCalibFactorUnits())."\">".$calib->getCalibFactor()."</calibFactor>";
   				$output .= "\t\t<reference units=\"".xmlEncode($calib->getReferenceUnits())."\">".$calib->getReference()."</reference>";
   				$output .= "\t\t<min>".$calib->getMinMeasuredValue()."</min>";
   				$output .= "\t\t<max>".$calib->getMaxMeasuredValue()."</max>";
   				$output .= "\t\t<minmaxunits>".xmlEncode($calib->getMeasuredValueUnits())."</minmaxunits>";
   				$output .= "\t</calibration>";
    		 }
     		 $output .= "\t</ChannelCalibrations>\n";
      	}
     }

    }
    */

    $output .= "</daqconfig>\n";
    return $output;
  }
}
?>
