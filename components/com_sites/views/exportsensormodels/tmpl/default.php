<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

require_once "lib/data/SensorModel.php";
require_once "Spreadsheet/Excel/Writer.php";

    $sensorModelsList = sensorModelPeer::findAll();
    $workbookName = "SensorModelsList";
    $exportFileName = $workbookName . ".xls";

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

    $columns = SensorModel::getExcelColumnNames();

    foreach ($columns as $k=>$v) {
      $worksheet->write(0, $k - 1, $v);
    }



    /*
    // The field headers
    $worksheet->write(0, 0, 'Name');
    $worksheet->write(0, 1, 'Type');
    $worksheet->write(0, 2, 'Manufacturer');
    $worksheet->write(0, 3, 'Model');
    $worksheet->write(0, 4, 'Description');
    $worksheet->write(0, 5, 'Signal Type');
    $worksheet->write(0, 6, 'Min Measured Value');
    $worksheet->write(0, 7, 'Max Measured Value');
    $worksheet->write(0, 8, 'Measured Value Unit');
    $worksheet->write(0, 9, 'Sensitivity');
    $worksheet->write(0, 10, 'Sensitivity Unit');
    $worksheet->write(0, 11, 'Min Operating Temperature');
    $worksheet->write(0, 12, 'Max Operating Temperature');
    $worksheet->write(0, 13, 'Operating Temperature Unit');
    $worksheet->write(0, 14, 'Note');
    */

    $line = 1;
    foreach($sensorModelsList as $sm) {
		$sensorType = $sm->getSensorType() ? $sm->getSensorType()->getName() : "";
		$measuredValueUnit = $sm->getMeasuredValueUnits() ? $sm->getMeasuredValueUnits()->getName() : "";
		$sensitivityUnit = $sm->getSensitivityUnits() ? $sm->getSensitivityUnits()->getName() : "";
		$tempUnit = $sm->getTempUnits() ? $sm->getTempUnits()->getName() : "";

		$worksheet->write($line, 0, $sm->getName());
		$worksheet->write($line, 1, $sensorType);
		$worksheet->write($line, 2, $sm->getManufacturer());
		$worksheet->write($line, 3, $sm->getModel());
		$worksheet->write($line, 4, $sm->getDescription());
		$worksheet->write($line, 5, $sm->getSignalType());
		$worksheet->write($line, 6, $sm->getMinMeasuredValue());
		$worksheet->write($line, 7, $sm->getMaxMeasuredValue());
		$worksheet->write($line, 8, $measuredValueUnit);
		$worksheet->write($line, 9, $sm->getSensitivity());
		$worksheet->write($line, 10, $sensitivityUnit);
		$worksheet->write($line, 11, $sm->getMinOpTemp());
		$worksheet->write($line, 12, $sm->getMaxOpTemp());
		$worksheet->write($line, 13, $tempUnit);
		$worksheet->write($line, 14, $sm->getNote());

		$line++;
    }
    // sending HTTP headers
    $workbook->send($exportFileName);

    // Let's send the file
    $workbook->close();

    exit;

?>


