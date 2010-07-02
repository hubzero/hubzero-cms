<?php
/**
 * Override the SDO renderer so that we can read the timeseries output data
 * for the Controller channel, and include it as a <TimeSeries> XML element.
 *
 * TODO: refactor this and DAQChannelSDORenderer into ChannelSDORenderer
 * and two subclasses...
 */

require_once 'lib/render/SDORenderer.php';

class ControllerChannelSDORenderer extends SDORenderer {

  private function renderAFile($dataFile, $objxml, BaseObject $channel) {
    $theFilename = $dataFile->getFullPath();
    if (!$theFilename) {
      return;
    }

    if (! is_readable($theFilename)) {
      throw new Exception("Could not open file $theFilename");
    }

    // We are able to read the data file.  Create a timeseries element.
    $timeseries = $objxml->createDataObject("TimeSeries");
    $timeseries->link = $dataFile->getRESTURI();
    $channelIndex = $channel->getChannelIndex()+2; // Plus one for awk's one-based index, plus an extra one to skip the time column.

    // Pick out the two columns that we care about (2 columns, time and value.)
    $timesAndValues = shell_exec("awk '/[[:digit:]]/ {print \$1, \$$channelIndex}' $theFilename");
    $timesAndValues = split("\n", $timesAndValues);

    // Create a sample for each row of the file.
    $i = 1;
    foreach ($timesAndValues as $timeAndValue) {
      $row = preg_split("/\s/", $timeAndValue);
      $sample = $timeseries->createDataObject("Sample");

      if (count($row) == 2) {
        $sample->time = $row[0];
        $sample->value = $row[1];
      }
      $i++;
    }

    return;
  }


  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->ControllerChannel[0];

    // Figure out where the data file is, and which column we need to read out of it.
    $dataFiles = $obj->getDataFiles();
    foreach ($dataFiles as $dataFile) {
      $this->renderAFile($dataFile, $objxml, $obj);
    }

    return array($das, $doc);
  }

}

?>
