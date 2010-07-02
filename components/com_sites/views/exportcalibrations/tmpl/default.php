<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

require_once "lib/data/Calibration.php";
require_once "lib/data/Sensor.php";
require_once "Spreadsheet/Excel/Writer.php";

	$facilityID = JRequest::getVar('facid');
	$facility = FacilityPeer::find($facilityID);
	
	if(is_null($facility)) 
	{
		return JError::raiseError( 404, "No facid found" );
	}

    $facid = $facility->getId();
    $facShortName = $facility->getShortName();

    $is_template = JRequest::getVar('template', false);
    $workbookName = "CalibrationsList";

    $exportFileName = $is_template ? $workbookName . "-" . $facShortName . "-template.xls" : $workbookName . "-" . $facShortName . ".xls";

    $doc =& JFactory::getDocument();
	$doc->setMimeEncoding('application/ms-excel');
    
    //header("Content-type: application/octet-stream");
    header('Content-Disposition: attachment; filename="' . $exportFileName . '"');
    header("Cache-Control: cache, must-revalidate");  // Do not remove this, if removed, IE won't work
    header("Pragma: cache");                          // Do not remove this, if removed, IE won't work
    //header("Content-Type: application/ms-excel");
    header("Expires: 0");
    ob_clean();

  
    // Creating a workbook
    $workbook = new Spreadsheet_Excel_Writer();

    // Creating a worksheet
    $worksheet =& $workbook->addWorksheet($workbookName);

    $columns = Calibration::getExcelColumnNames();

    foreach ($columns as $k=>$v) {
      $worksheet->write(0, $k - 1, $v);
    }

    $line = 1;

    if($is_template) {
      $sensorsList = SensorPeer::findByFacility($facid);

      foreach($sensorsList as $s) {
        $worksheet->write($line, 1, $s->getName());
        $line++;
      }
    }
    else {
      $calibrationList = CalibrationPeer::findByFacility($facid);

      foreach($calibrationList as $calibration) {

        $measuredValueUnit = $calibration->getMeasuredValueUnits() ? $calibration->getMeasuredValueUnits() : "";
        $sensitivityUnit = $calibration->getSensitivityUnits() ? $calibration->getSensitivityUnits() : "";
        $referenceUnit = $calibration->getReferenceUnits() ? $calibration->getReferenceUnits() : "";
        $calibrationFactorUnit = $calibration->getCalibFactorUnits() ? $calibration->getCalibFactorUnits() : "";

        $worksheet->write($line, 0, $calibration->getId());
        $worksheet->write($line, 1, $calibration->getSensor()->getName());
        $worksheet->write($line, 2, $calibration->getCalibDate('%m/%d/%Y'));
        $worksheet->write($line, 3, $calibration->getCalibrator());
        $worksheet->write($line, 4, $calibration->getAdjustments());
        $worksheet->write($line, 5, $calibration->getMinMeasuredValue());
        $worksheet->write($line, 6, $calibration->getMaxMeasuredValue());
        $worksheet->write($line, 7, $measuredValueUnit);
        $worksheet->write($line, 8, $calibration->getSensitivity());
        $worksheet->write($line, 9, $sensitivityUnit);
        $worksheet->write($line, 10, $calibration->getReference());
        $worksheet->write($line, 11, $referenceUnit);
        $worksheet->write($line, 12, $calibration->getCalibFactor());
        $worksheet->write($line, 13, $calibrationFactorUnit);
        $worksheet->write($line, 14, $calibration->getDescription());

        $line++;
      }
    }
    // sending HTTP headers
    $workbook->send($exportFileName);

    // Let's send the file
    $workbook->close();

    exit;


?>


