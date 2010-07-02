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
    
    $sensorList = SensorPeer::findByFacility($facid);

    $workbookName = "SensorsList";

    $exportFileName = $workbookName . "-" . $facShortName . ".xls";

    $doc =& JFactory::getDocument();
	$doc->setMimeEncoding('application/ms-excel');
    
    header('Content-Disposition: attachment; filename="' . $exportFileName . '"');
    header("Cache-Control: cache, must-revalidate");  // Do not remove this, if removed, IE won't work
    header("Pragma: cache");                          // Do not remove this, if removed, IE won't work
    header("Expires: 0");
    ob_clean();
    
    // Creating a workbook
    $workbook = new Spreadsheet_Excel_Writer();

    // Creating a worksheet
    $worksheet =& $workbook->addWorksheet($workbookName);

    $columns = Sensor::getExcelColumnNames();

    foreach ($columns as $k=>$v) {
      $worksheet->write(0, $k - 1, $v);
    }

    /*
    // The field headers
    $worksheet->write(0, 0, 'Sensor Name');
    $worksheet->write(0, 1, 'Sensor Model');
    $worksheet->write(0, 2, 'Serial Number');
    $worksheet->write(0, 3, 'Local Id');
    $worksheet->write(0, 4, 'Supplier');
    $worksheet->write(0, 5, 'Commission Date (MM-DD-YYYY)');
    $worksheet->write(0, 6, 'Decommission Date (MM-DD-YYYY)');
    */

    if(!$is_template) {
      $line = 1;
      foreach($sensorList as $sensor) {
        $sm = $sensor->getSensorModel()->getName();
        $worksheet->write($line, 0, $sensor->getName());
        $worksheet->write($line, 1, $sensor->getSensorModel()->getName());
        $worksheet->write($line, 2, $sensor->getSerialNumber());
        $worksheet->write($line, 3, $sensor->getLocalId());
        $worksheet->write($line, 4, $sensor->getSupplier());
        $worksheet->write($line, 5, $sensor->getCommissionDate('%m-%d-%Y'));
        $worksheet->write($line, 6, $sensor->getDecommissionDate('%m-%d-%Y'));

        $line++;
      }
    }
    // sending HTTP headers
    $workbook->send($exportFileName);

    // Let's send the file
    $workbook->close();

    exit;


?>


