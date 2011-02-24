<?php
/**
 * Primary controller file for the sites component 
 * 
 * @package		NEEShub 
 * @author		David Benham (dbenham@purdue.edu)
 * @copyright	Copyright 2010 by NEESCommIT
 */
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');



/**
 *Facility Component Controller
 *
 * @package    NEEShub
 * @subpackage Components
 */
class SitesController extends JController
{

    function __construct()
    {
        parent::__construct();
        $this->registerTask( 'editcontactperson' , 'editcontactperson' );
        $this->registerTask( 'savecontactperson' , 'savecontactperson' );
        $this->registerTask( 'editsite' , 'editsite' );
        $this->registerTask( 'savesite' , 'savesite' );
        $this->registerTask( 'editcontactrolesandpermissions' , 'editcontactrolesandpermissions' );

        $this->registerTask('savecontactrolesandpermissions' , 'savecontactrolesandpermissions');
        $this->registerTask('savesite' , 'savesite');
        $this->registerTask('savefile' , 'savefile');
        $this->registerTask('deletefile', 'deletefile');
        $this->registerTask('addsitemembership', 'addsitemembership');
        $this->registerTask('deletesitemembership', 'deletesitemembership');
        $this->registerTask('savesensor', 'savesensor');
        $this->registerTask('savesensormodel', 'savesensormodel');
        $this->registerTask('saveequipment', 'saveequipment');
        $this->registerTask('saveequipmentdocfile', 'saveequipmentdocfile');
        $this->registerTask('savesensordocfile', 'savesensordocfile');
        $this->registerTask('savesensormodeldocfile', 'savesensormodeldocfile');
        $this->registerTask('savecalibrationdocfile', 'savecalibrationdocfile');
        $this->registerTask('savecalibration', 'savecalibration');
        $this->registerTask('deletecalibration', 'deletecalibration');
        $this->registerTask('saveequipmentmodel', 'saveequipmentmodel');
        $this->registerTask('deleteequipmentmodel', 'deleteequipmentmodel');
        $this->registerTask('saveequipmentmodelfile', 'saveequipmentmodelfile');
        $this->registerTask('uploadsensors' , 'uploadsensors');
        $this->registerTask('uploadsensorcalibrations' , 'uploadsensorcalibrations');
        $this->registerTask('savesitecontact' , 'savesitecontact');

    }



    function savesitecontact()
    {
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $new_contact_id = JRequest::getVar('new_contact_id');

        // This is hard enough to warrant 500 code, no legit reason to get here unless
        // you are hacking or a major problem has occured
        $canEdit = FacilityHelper::canEdit($facility);
        if(!$canEdit)
            return JError::raiseError( 500, "You do not have access to update the site contact for this facility");

        // Get new contact
        $new_contact = PersonPeer::find($new_contact_id);

        // Get previous contact
        $roles = RolePeer::findByName("Site Contact");
        $role = $roles[0];

        // Get a list of people with "Site Contact" role on the facility.
        $PERs = PersonEntityRolePeer::findByEntityRole($facility, $role);
        /* @var $current_contact Contact */
        if(count($PERs) > 0)
            $current_contact = $PERs[0]->getPerson();
        else
            $current_contact = null;

        if(!empty($current_contact))
            $current_contact->deleteRoleForEntity($role, $facility);

        $new_contact->addRoleForEntity($role, $facility);

        $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=contact'));
        $this->redirect();
        
    }


    function uploadsensorcalibrations()
    {

        $msg = '';
        $errorMsg = '';
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canEdit = FacilityHelper::canEdit($facility);
        $canCreate = FacilityHelper::canCreate($facility);

        // This is hard enough to warrant 500 code, no legit reason to get here unless
        // you are hacking or a major problem has occured
        $canCreate = FacilityHelper::canCreate($facility);
        if(!$canEdit)
            return JError::raiseError( 500, "You do not have access to bulk add sensor calibrations for this facility");


        // Why bother without an input file?
        if (!$_FILES['uploadFile']['size'] == 0) {

            // Keep an array of all of the objects we're creating
            $objectsArray = array();

            $newFilePath = "/tmp/" . time() . "_" . $_FILES["uploadFile"]["name"];
            move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $newFilePath);

            $reader = new FileUploadReader($newFilePath);

            $cells = $reader->getData();

            if (!is_array($cells)) {
                $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Unable to parse your uploaded data file. Make sure you are uploading a valid document.<br/>Remember, the accepted formats are Excel (95, 97, 2000, 2003) (*.xls), and comma-delimited text files (*.csv), and tab-delimited text files (*.txt), and XML Speadsheet (*.xml)";
            }

            $columns = Calibration::getExcelColumnNames();
            /*
              1=>"Calibration ID",
              2=>"Sensor Name",
              3=>"Date (M[M]/D[D]/YYYY)",
              4=>"Person",
              5=>"Adjustments Value",
              6=>"Min. Measured Value",
              7=>"Max. Measured Value",
              8=>"Measured Value Unit",
              9=>"Sensitivity Value",
              10=>"Sensitivity Unit",
              11=>"Reference Value",
              12=>"Reference Unit",
              13=>"Calibration Factor",
              14=>"Calibration Factor Unit",
              15=>"Description");
             */

            $numRows = count($cells);
            $numCols = isset($cells[1]) ? count($cells[1]) : 0;

            $tempMsg = "Row does not have correct number of columns. <br/>These columns must be: " . implode(", ", $columns) . " <br/>and must be in the same of this order";

            if ($numCols != count($columns)) {
                $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . $tempMsg;
            }

            for ($col = 1; $col <= count($columns); $col++) {
                if ($cells[1][$col] != $columns[$col]) {
                    $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . $tempMsg;
                }
            }

            $sensorNameMap = array();
            $sensors = SensorPeer::findByFacility($facilityID);

            foreach ($sensors as $sensor) {
                $sensorNameMap[strtolower($sensor->getName())] = $sensor;
            }


            $calibIdMap = array();
            $calibs = CalibrationPeer::findByFacility($facilityID);

            foreach ($calibs as $cal) {
                $calibIdMap[strtolower($cal->getId())] = $cal;
            }

            $count_update = 0;
            $count_new = 0;

            if(empty($errorMsg))
            {
                // Start from Line 2, as Line 1 is the header
                for ($row = 2; $row <= $numRows; $row++) {

                    $calibId = $cells[$row][1];
                    $sensorName = strtolower($cells[$row][2]);
                    $calibDate = $cells[$row][3];
                    $calibrator = $cells[$row][4];
                    $adjustmentsValue = $cells[$row][5];
                    $minMeasuredValue = $cells[$row][6];
                    $maxMeasuredValue = $cells[$row][7];
                    $measuredValueUnit = $cells[$row][8];
                    $sensitivityValue = $cells[$row][9];
                    $sensitivityUnit = $cells[$row][10];
                    $referenceValue = $cells[$row][11];
                    $referenceUnit = $cells[$row][12];
                    $calibFactor = $cells[$row][13];
                    $calibFactorUnit = $cells[$row][14];
                    $description = $cells[$row][15];

                    // Existing Calibration, update this
                    if (!empty($calibId)) {
                        if (!key_exists($calibId, $calibIdMap)) {
                           $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: The calibration ID '$calibId' not found in the database. Please do not modify the Calibration ID from your spreadsheet template";
                            break;
                        }

                        $calib = $calibIdMap[$calibId];
                        $count_update++;
                    } else {
                        $calib = new Calibration();
                        $count_new++;
                    }

                    if (!key_exists($sensorName, $sensorNameMap)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Sensor '$sensorName' not found in the facility sensor list.";
                        break;
                    }

                    $orig_calibDate = $calibDate;

                    // Users may use MM-DD-YYYY insted of MM/DD/YY, give it a chance
                    $calibDate = str_replace("-", "/", $calibDate);

                    $dateTokens = explode('/', $calibDate);

                    if (sizeof($dateTokens) != 3) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Please enter a valid date. (MM/DD/YYYY)";
                        break;
                    }

                    $mm = $dateTokens[0] + 0;
                    $dd = $dateTokens[1] + 0;
                    $yyyy = $dateTokens[2] + 0;

                    if (!is_int($yyyy) || !is_int($mm) || !is_int($dd) || !checkdate($mm, $dd, $yyyy)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Please enter a valid date. (MM/DD/YYYY). Your entry is: $orig_calibDate";
                        break;
                    }

                    $calibDateDB = date("Y-m-d", strtotime($mm . "/" . $dd . "/" . $yyyy));

                    if (strlen($adjustmentsValue) > 0 && !is_numeric($adjustmentsValue)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Please enter a valid adjustments value";
                        break;
                    }

                    if (strlen($minMeasuredValue) > 0 && !is_numeric($minMeasuredValue)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Please enter a valid min measured value";
                        break;
                    }

                    if (strlen($maxMeasuredValue) > 0 && !is_numeric($maxMeasuredValue)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Please enter a valid max measured value";
                        break;
                    }

                    if (strlen($sensitivityValue) > 0 && !is_numeric($sensitivityValue)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Please enter a valid sensitivity value";
                        break;
                    }

                    if (strlen($referenceValue) > 0 && !is_numeric($referenceValue)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Please enter a valid reference value";
                        break;
                    }


                    if ($adjustmentsValue == "")
                        $adjustmentsValue = null;
                    if ($minMeasuredValue == "")
                        $minMeasuredValue = null;
                    if ($maxMeasuredValue == "")
                        $maxMeasuredValue = null;
                    if ($sensitivityValue == "")
                        $sensitivityValue = null;
                    if ($referenceValue == "")
                        $referenceValue = null;

                    $sensor = $sensorNameMap[$sensorName];
                    $sensorId = $sensor->getId();

                    $calib->setSensor($sensor);
                    $calib->setCalibDate($calibDateDB);
                    $calib->setCalibrator($calibrator);
                    $calib->setDescription($description);
                    $calib->setAdjustments($adjustmentsValue);
                    $calib->setMinMeasuredValue($minMeasuredValue);
                    $calib->setMaxMeasuredValue($maxMeasuredValue);
                    $calib->setMeasuredValueUnits($measuredValueUnit);
                    $calib->setSensitivity($sensitivityValue);
                    $calib->setSensitivityUnits($sensitivityUnit);
                    $calib->setReference($referenceValue);
                    $calib->setReferenceUnits($referenceUnit);
                    $calib->setCalibFactor($calibFactor);
                    $calib->setCalibFactorUnits($calibFactorUnit);
                    $calib->setDeleted(0);

                    $objectsArray[] = $calib;

                } // end for loop
            }

            // Persist objects to database
            foreach ($objectsArray as $obj) {
                $obj->save();
            }

            $msg .= (strlen($msg) > 0 ? '<br/>' : '') . "Your calibrations list was uploaded successfully.";
            $msg .= (strlen($msg) > 0 ? '<br/>' : '') . "<br/>Total $count_new calibration(s) have been inserted.";
            $msg .= (strlen($msg) > 0 ? '<br/>' : '') . "<br/>Total $count_update calibration(s) have been updated.";

        }// end if check for submitted file
        else
        {
             $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . 'No input file provided';
        }

        //echo $errorMsg;
        //return;

       $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=uploadsensorcalibrations&msg=' . $msg . '&errorMsg=' . $errorMsg, false));
       $this->redirect();

    }

    

    function deleteSensor()
    {

        $facilityID = JRequest::getVar('id');
        $sensorID = Jrequest::getVar('sensorid');
        $facility = FacilityPeer::find($facilityID);

        // This is hard enough to warrant 500 code, no legit reason to get here unless
        // you are hacking or a major problem has occured
        $canDelete = FacilityHelper::canDelete($facility);
        if(!$canDelete)
            return JError::raiseError( 500, "You do not have access to delete sensors for this facility");

        $sensor = SensorPeer::find($sensorID);
        if (!empty($sensor))
        {
            $sensor->setDeleted(true);
            $sensor->save();
        }

       $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=sensors&msg=' . $msg . '&errorMsg=' . $errorMsg, false));
       $this->redirect();


    }


    function uploadsensors()
    {

        $msg = '';
        $errorMsg = '';
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canEdit = FacilityHelper::canEdit($facility);
        $canCreate = FacilityHelper::canCreate($facility);

        // This is hard enough to warrant 500 code, no legit reason to get here unless
        // you are hacking or a major problem has occured
        $canCreate = FacilityHelper::canCreate($facility);
        if(!$canEdit)
            return JError::raiseError( 500, "You do not have access to bulk add sensors for this facility");

        // Keep an array of all of the objects we're creating
        $objectsArray = array();

        // Why bother without an input file?
        if (!$_FILES['uploadFile']['size'] == 0) {

            $newFilePath = "/tmp/" . time() . "_" . $_FILES["uploadFile"]["name"];
            move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $newFilePath);

            $reader = new FileUploadReader($newFilePath);

            $cells = $reader->getData();

            if (!is_array($cells)) {
                 $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Unable to parse your uploaded data file. Make sure you are uploading a valid document.<br/>Remember, the accepted formats are Excel (95, 97, 2000, 2003) (*.xls), and comma-delimited text files (*.csv), and tab-delimited text files (*.txt), and XML Speadsheet (*.xml)";
            }

            $columns = Sensor::getExcelColumnNames();
            /*
              array(
              1=>"Sensor Name",
              2=>"Sensor Model",
              3=>"Serial Number",
              4=>"Local Id",
              5=>"Supplier",
              6=>"Commission Date (MM-DD-YYYY)",
              7=>"Decommission Date (MM-DD-YYYY)");
             */

            $numRows = count($cells);
            $numCols = isset($cells[1]) ? count($cells[1]) : 0;

            $tempErrorMsg = "Row does not have correct number of columns. <br/>These columns must be: " . implode(", ", $columns) . " <br/>and must be in the same of this order";

            if ($numCols != count($columns)) {
                 $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . $tempErrorMsg;
            }

            for ($col = 1; $col <= count($columns); $col++) {
                if ($cells[1][$col] != $columns[$col]) {
                     $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . $tempErrorMsg;
                }
            }

            // Only proceed if the input file format checks above passed
            if(empty($errorMsg))
            {
                $newSensorMap = array();
                $sensorNameMap = array();
                $sensorModelNameMap = array();

                $sensors = SensorPeer::findByFacility($facilityID);
                foreach ($sensors as $sensor) {
                    $sensorNameMap[strtolower($sensor->getName())] = $sensor;
                }

                $sensorModels = SensorModelPeer::findAll();
                foreach ($sensorModels as $sm) {
                    $sensorModelNameMap[strtolower(trim($sm->getName()))] = $sm;
                }

                // Start from Line 2, as Line 1 is the header
                for ($row = 2; $row <= $numRows; $row++) {
                    $sensorName = $cells[$row][1];
                    $sensorModelName = $cells[$row][2];
                    $serialNumber = $cells[$row][3];
                    $localId = $cells[$row][4];
                    $supplier = $cells[$row][5];
                    $commissionDate = $cells[$row][6];
                    $decommissionDate = $cells[$row][7];

                    if (empty($sensorName)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Sensor Name cannot be empty.";
                        break;
                    }

                    if (empty($sensorModelName)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: SensorModel Name cannot be empty.";
                        break;
                    }

                    if (!key_exists(strtolower($sensorModelName), $sensorModelNameMap)) {
                        $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Sensor must be associated with a SensorModel. This SensorModel '$sensorModelName' is not found in database.";
                        break;
                    }

                    if (empty($commissionDate)) {
                        $commissionDate = null;
                    } else {
                        if (!preg_match('/^(00|0[1-9]|1[012])-(00|0[1-9]|[12][0-9]|3[01])-(0000|19\d\d|2\d\d\d)$/', $commissionDate, $matches1)) {
                            $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Please enter a valid date for commission Date. (MM-DD-YYYY)";
                            break;
                        } else {
                            $commissionDate = $matches1[3] . "-" . $matches1[1] . "-" . $matches1[2];
                        }
                    }

                    if (empty($decommissionDate)) {
                        $decommissionDate = null;
                    } else {
                        if (!preg_match('/^(00|0[1-9]|1[012])-(00|0[1-9]|[12][0-9]|3[01])-(0000|19\d\d|2\d\d\d)$/', $decommissionDate, $matches2)) {
                            $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Error line $row: Please enter a valid date for decommission Date. (MM-DD-YYYY)";
                            break;
                        } else {
                            $decommissionDate = $matches2[3] . "-" . $matches2[1] . "-" . $matches2[2];
                        }
                    }

                    $serialNumber = $cells[$row][3];
                    $localId = $cells[$row][4];
                    $supplier = $cells[$row][5];

                    if (empty($serialNumber))
                        $serialNumber = null;
                    if (empty($localId))
                        $localId = null;
                    if (empty($supplier))
                        $supplier = null;

                    if (key_exists(strtolower($sensorName), $sensorNameMap)) {
                        $sensorObj = $sensorNameMap[strtolower($sensorName)];
                    } else {
                        $sensorObj = new Sensor();
                        $newSensorMap[] = $sensorObj;
                    }

                    $sensorObj->setSensorModel($sensorModelNameMap[strtolower($sensorModelName)]);
                    $sensorObj->setName($sensorName);
                    $sensorObj->setSerialNumber($serialNumber);
                    $sensorObj->setLocalId($localId);
                    $sensorObj->setSupplier($supplier);
                    $sensorObj->setCommissionDate($commissionDate);
                    $sensorObj->setDecommissionDate($decommissionDate);
                    $sensorObj->setDeleted(0);

                    $objectsArray[] = $sensorObj;

                } // end primary for loop processing

                // This will not be true if we broke out of the above loop early for any reason.
                // This is the final save to commit the sensors to the database
                if(empty($errorMsg))
                {

                    foreach ($objectsArray as $aSensor)
                    {
                        $aSensor->save();
                    }

                    foreach ($newSensorMap as $newSensor)
                    {
                        $newSensor->addToFacility($facility);
                    }

                
                }


            } // end if check for format issues on input file

            // minus one b/c of that pesky excel header
            $msg = $numRows-1 . ' sensors imported';

        }// end if check for submitted file
        else
        {
             $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . 'No input file provided';
        }

       $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=uploadsensors&msg=' . $msg . '&errorMsg=' . $errorMsg, false));
       $this->redirect();



    }


    function saveequipmentmodelfile()
    {
        $equipmentmodelid = JRequest::getVar('equipmentmodelid');
        $equipmentmodelfiletype = JRequest::getVar('equipmentmodelfiletype');
        $documentTitle = JRequest::getVar('documentTitle');
        $documentDesc = JRequest::getVar('documentDesc');

        $equipmentmodel = EquipmentModelPeer::find($equipmentmodelid);
        $destpath = $equipmentmodel->getPathname(). "/Documentation";

        $datafile = DataFile::newDataFileByUpload($_FILES["documentFile"], $destpath);
        $datafile->setTitle($documentTitle);
        $datafile->setDescription($documentDesc);
        $datafile->save();

        // Specific processing required for the equipmentmodel
        switch($equipmentmodelfiletype)
        {
            case 0:
                $equipmentmodel->setAdditionalSpecFile($datafile);
                break;
            case 1:
                $equipmentmodel->setManufacturerDocFile($datafile);
                break;
            case 2:
                $equipmentmodel->setDesignConsiderationFile($datafile);
                break;
            case 3:
                $equipmentmodel->setSubcomponentsDocFile($datafile);
                break;
            case 4:
                $equipmentmodel->setInterfaceDocFile($datafile);
                break;
        }
        
        $equipmentmodel->save();

        $redirectURL = JRequest::getVar('redirectURL');
        $this->_redirect = $redirectURL;


    }




    function deleteequipmentmodel()
    {
        $equipmentmodelid = JRequest::getVar('equipmentmodelid', '');
        $equpmentmodel = EquipmentModelPeer::find($equipmentmodelid);

        $equpmentmodel->delete();

        // show the sensor page
        JRequest::setVar('view','majorequipment');
        parent::display();

    }


    function saveequipmentmodel()
    {
        $equipmentmodelID = JRequest::getVar('equipmentmodelid','');
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canEdit = FacilityHelper::canEdit($facility);
        $canCreate = FacilityHelper::canCreate($facility);


        // Check for cancel button click, go back to the majorequipement view for the previous facility
        $submitbutton = JRequest::getVar('submitbutton', '');
        if($submitbutton == 'Cancel')
        {
            JRequest::setVar('view','majorequipment');
            $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&view=majorequipment&id=' . $facilityID, false ));
            $this->redirect();
            return;
        }


        if(!FacilityHelper::isAdmin())
        {
            return JError::raiseError( 500, 'You do not have access to edit sensor calibrations for this facility');
        }

        // These two errors are hard enough to warrant 500 codes, no legit reason to get here unless
        // you are hacking or a major problem has occured
        if(!$canEdit && $equipmentmodelID > -1)
            return JError::raiseError( 500, 'You do not have access to edit equipment models');

        if(!$canCreate  && $equipmentmodelID == -1)
            return JError::raiseError( 500, 'You do not have access to add equipment models');


        // either -1 or > 0, if neither, then we dont' know what to do
        if(empty($equipmentmodelID))
        {
            return JError::raiseError( 500, 'Cannot determine equipmentmodelid');
        }

        $equipmentClassId = JRequest::getVar('equipmentClassId');
        $name = JRequest::getVar('name');
        $manufacturer = JRequest::getVar('manufacturer');
        $supplier = JRequest::getVar('supplier');
        $modelnumber = JRequest::getVar('modelnumber');

        //echo $equipmentmodelID;
        //return;

        if($equipmentmodelID == -1) //add
            $equipmentModel = new EquipmentModel();
        elseif($equipmentmodelID > 0) // update
            $equipmentModel = EquipmentModelPeer::find($equipmentmodelID);

        $equipmentModel->setEquipmentClass(EquipmentClassPeer::find($equipmentClassId));
        $equipmentModel->setName($name);
        $equipmentModel->setManufacturer($manufacturer);
        $equipmentModel->setSupplier($supplier);
        $equipmentModel->setModelNumber($modelnumber);
        $equipmentModel->save();

        //JRequest::setVar('equipmentmodelid', $equipmentModel->getId(), 'post', true);
        //JRequest::setVar('view','editequipmentmodel');
        //parent::display();

       $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=editequipmentmodel&equipmentmodelid=' . $equipmentModel->getId() . '&msg=Save+Successful', false));
       $this->redirect();



    }


    function deletecalibration()
    {
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canDelete = FacilityHelper::canDelete($facility);
        $calibrationID = JRequest::getVar('calibrationid', -1);

        if($calibrationID == -1)
            return JError::raiseError( 500, "No calibrationid available");

        if(!$canDelete)
            return JError::raiseError( 500, "You do not have access to delete sensor calibrations");

        $calibration = CalibrationPeer::find($calibrationID);
        $calibration->delete();

        // show the sensor page
        JRequest::setVar('view','sensor');
        parent::display();
    }


    function savecalibration()
    {

        $msg = '';
        $errorMsg = '';

        $calibrationID = JRequest::getVar('calibrationid', -1);
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $sensorID = JRequest::getVar('sensorid');
        $canEdit = FacilityHelper::canEdit($facility);
        $canCreate = FacilityHelper::canCreate($facility);

        // Check for cancel button click
        $submitbutton = JRequest::getVar('submitbutton', '');
        if($submitbutton == 'Cancel')
        {
            JRequest::setVar('view','sensors');
            $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&view=calibration&id=' . $facilityID . '&sensorid=' . $sensorID . '&calibrationid=' . $calibrationID, false ));
            $this->redirect();
            return;
        }

        // These two errors are hard enough to warrant 500 codes, no legit reason to get here unless
        // you are hacking or a major problem has occured
        if(!$canEdit && $sensorModelID > -1)
            return JError::raiseError( 500, "You do not have access to edit sensor calibrations for this facility");

        if(!$canCreate  && $sensorModelID == -1)
            return JError::raiseError( 500, "You do not have access to add sensor calibration for this facility");

        /* @var $calibration Calibration */
        if($calibrationID == -1)
        {
            $calibration = new Calibration ();
            $sensor = SensorPeer::find($sensorID);
            $calibration->setSensor($sensor);
        }
        else
            $calibration = CalibrationPeer::find ($calibrationID);

        //Grab the form fields
        $CalibDate = JRequest::getVar('CalibDate', '');
        $calibrator = JRequest::getVar('calibrator', '');
        $description = JRequest::getVar('description', '');
        $adjustments = JRequest::getVar('adjustments', '');
        $minMeasuredValue = JRequest::getVar('minMeasuredValue', '');
        $maxMeasuredValue = JRequest::getVar('maxMeasuredValue', '');
        $measuredValueUnits = JRequest::getVar('measuredValueUnits', '');
        $sensitivity = JRequest::getVar('sensitivity', '');
        $sensitivityUnits = JRequest::getVar('sensitivityUnits', '');
        $reference = JRequest::getVar('reference', '');
        $referenceUnits = JRequest::getVar('referenceUnits', '');
        $calibFactor = JRequest::getVar('calibFactor', '');
        $calibFactorUnits = JRequest::getVar('calibFactorUnits', '');

        //**** Validation
        $oracleFormattedCalibrationDate = '';
        if(empty($CalibDate))
            $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . 'Date is a required field';
        elseif (!$this->validateDateTime($CalibDate, $oracleFormattedCalibrationDate))
            $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . 'Please enter a valid calibration date';

        if (strlen($adjustments) > 0 && !is_numeric($adjustments))
            $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Please enter a valid adjustments value";

        if (strlen($minMeasuredValue) > 0 && !is_numeric($minMeasuredValue))
            $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Please enter a valid min measured value";

        if (strlen($maxMeasuredValue) > 0 && !is_numeric($maxMeasuredValue))
            $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Please enter a valid max measured value";

        if (strlen($sensitivity) > 0 && !is_numeric($sensitivity))
            $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Please enter a valid sensitivity value";

        if (strlen($reference) > 0 && !is_numeric($reference))
            $errorMsg .= (strlen($errorMsg) > 0 ? '<br/>' : '') . "Please enter a valid reference value";

        
        // null is prettier than blank
        if(empty($adjustments)) $adjustments = null;
        if(empty($minMeasuredValue)) $minMeasuredValue = null;
        if(empty($maxMeasuredValue)) $maxMeasuredValue = null;
        if(empty($sensitivity)) $sensitivity = null;
        if(empty($reference)) $reference = null;

        // Only save if we are error free
        if ( empty($errorMsg) )
        {
            $calibration->setCalibDate($oracleFormattedCalibrationDate);
            $calibration->setCalibrator($calibrator);
            $calibration->setAdjustments($adjustments);
            $calibration->setMinMeasuredValue($minMeasuredValue);
            $calibration->setMaxMeasuredValue($maxMeasuredValue);
            $calibration->setMeasuredValueUnits($measuredValueUnits);
            $calibration->setSensitivity($sensitivity);
            $calibration->setSensitivityUnits($sensitivityUnits);
            $calibration->setReference($reference);
            $calibration->setReferenceUnits($referenceUnits);
            $calibration->setCalibFactor($calibFactor);
            $calibration->setCalibFactorUnits($calibFactorUnits);

            $calibration->save();

            if($calibrationID == -1)
            {
                $msg = 'Calibration successfully added';

                // The refresh of this page will turn it from an add page to an edit page
                $calibrationID = $calibration->getId();
                JRequest::setVar('$calibrationid', $calibrationID);
                JRequest::setVar('view','sensor');

            }
            else
            {
                $msg = 'Calibrtaion successfully updated';
                JRequest::setVar('view','editcalibration');
            }
        }

        // if error, send all the previously submitted form values so the form doesn't get cleared out
        if(!empty($errorMsg))
        {
            JRequest::setVar('CalibDate', $CalibDate);
            JRequest::setVar('calibrator', $calibrator);
            JRequest::setVar('description', $description );
            JRequest::setVar('adjustments', $adjustments );
            JRequest::setVar('minMeasuredValue', $minMeasuredValue );
            JRequest::setVar('maxMeasuredValue', $maxMeasuredValue);
            JRequest::setVar('measuredValueUnits', $measuredValueUnits );
            JRequest::setVar('sensitivity', $sensitivity);
            JRequest::setVar('sensitivityUnits', $sensitivityUnits);
            JRequest::setVar('reference', $reference);
            JRequest::setVar('referenceUnits', $referenceUnits);
            JRequest::setVar('calibFactor', $calibFactor);
            JRequest::setVar('calibFactorUnits', $calibFactorUnits);
        }

        JRequest::setVar('msg', $msg);
        JRequest::setVar('errorMsg', $errorMsg);

        parent::display();
        
    }


    function savecalibrationdocfile()
    {
        $calibrationid = JRequest::getVar('calibrationid');
        $documentTitle = JRequest::getVar('documentTitle');
        $documentDesc = JRequest::getVar('documentDesc');

        $calibration = CalibrationPeer::find($calibrationid);
        $destpath = $calibration->getPathname() .  "/Documentation";

        $datafile = DataFile::newDataFileByUpload($_FILES["documentFile"], $destpath);
        $datafile->setTitle($documentTitle);
        $datafile->setDescription($documentDesc);
        $datafile->save();

        $redirectURL = JRequest::getVar('redirectURL');
        $this->_redirect = $redirectURL;
    }


    function savesensormodeldocfile()
    {
        $sensormodelid = JRequest::getVar('sensormodelid');
        $documentTitle = JRequest::getVar('documentTitle');
        $documentDesc = JRequest::getVar('documentDesc');

        $sensormodel = SensorModelPeer::find($sensormodelid);
        $destpath = $sensormodel->getPathname(). "/Documentation";

        $datafile = DataFile::newDataFileByUpload($_FILES["documentFile"], $destpath);
        $datafile->setTitle($documentTitle);
        $datafile->setDescription($documentDesc);
        $datafile->save();

        $redirectURL = JRequest::getVar('redirectURL');
        $this->_redirect = $redirectURL;
    }


    function savesensordocfile()
    {

        $sensorid = JRequest::getVar('sensorid');
        $documentTitle = JRequest::getVar('documentTitle');
        $documentDesc = JRequest::getVar('documentDesc');

        $sensor = SensorPeer::find($sensorid);
        $destpath = $sensor->getPathname(). "/Documentation";

        $datafile = DataFile::newDataFileByUpload($_FILES["documentFile"], $destpath);
        $datafile->setTitle($documentTitle);
        $datafile->setDescription($documentDesc);
        $datafile->save();

        $redirectURL = JRequest::getVar('redirectURL');
        $this->_redirect = $redirectURL;

    }


    function saveequipmentdocfile()
    {
        $equipmentid = JRequest::getVar('equipmentid');
        $documentTitle = JRequest::getVar('documentTitle');
        $documentDesc = JRequest::getVar('documentDesc');

        $equipment = EquipmentPeer::find($equipmentid);
        $destpath = $equipment->getPathname();

        $datafile = DataFile::newDataFileByUpload($_FILES["documentFile"], $destpath);
        $datafile->setTitle($documentTitle);
        $datafile->setDescription($documentDesc);
        $datafile->save();

        // This is sensor documentation, this is the only doc type that makes sense
        $doctype = DocumentTypePeer::findByName('Document');

        $docs = new EquipmentDocumentation($equipment, $documentTitle, $documentDesc, $doctype, null, $datafile);
        $docs->save();

        $redirectURL = JRequest::getVar('redirectURL');
        $this->_redirect = $redirectURL;
    }


    function saveequipment()
    {
        // Grab/define high level form variables
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canEdit = FacilityHelper::canEditSensorModel($facility);
        $canCreate = FacilityHelper::canCreateSensorModel($facility);
        $equipmentID = JRequest::getVar('equipmentid', -1);
        $errorMsg = '';
        $msg = '';

        // Check for cancel button click
        $submitbutton = JRequest::getVar('submitbutton', '');
        if($submitbutton == 'Cancel')
        {
            JRequest::setVar('view','sensors');
            $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&equipmentid=' . $equipmentID . '&view=majorequipment', false));
            $this->redirect();
            return;
        }

        // These two errors are hard enough to warrant 500 codes, no legit reason to get here unless
        // you are hacking or a major problem has occured
        if(!$canEdit && $equipmentID > -1)
            return JError::raiseError( 500, "You do not have access to edit equipment for this facility");

        if(!$canCreate  && $equipmentID == -1)
            return JError::raiseError( 500, "You do not have access to add equipment for this facility");


        // Grab form specific variables
        $name = trim(JRequest::getVar('name'));
        
        // Name Validation
        if(empty($name))
            $errorMsg = 'Name is a required field';

        // Commission date validation
        $commissiondate = JRequest::getVar('commissionDate');
        if(!empty($commisiondate))
        {
            preg_match ('/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/', $commissiondate, $matches_commissiondate);

            if(count($matches_commissiondate) == 4)
            {
                if(!checkdate($matches_commissiondate[1], $matches_commissiondate[2], $matches_commissiondate[3]))
                    $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Please enter a valid Commission Date';
            }
            else
               $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Please enter a valid Commission Date';
        }
        
        if(!empty($commisiondate))
            $commissiondate = $matches_commissiondate[3]."-".$matches_commissiondate[1]."-".$matches_commissiondate[2];


        //var_dump($commissiondate);
        //return;

        //**** Only save if we are error free
        if ( empty($errorMsg) )
        {
            // add or edit
            if($equipmentID == -1)
            {
                $equipment = new Equipment();
                $equipment->setOrganization($facility);
            }
            else
                $equipment = EquipmentPeer::find($equipmentID);

            // At this point, save and add are the same
            $parentequipmentid = JRequest::getVar("parentequipmentid", -1);
            //$name - already got name
            $equipmentModelID = JRequest::getVar("equipmentModelId");
            $neesOperated = JRequest::getVar("neesOperated");
            $separated = JRequest::getVar("separated");
            $owner = JRequest::getVar("owner");
            $sn = JRequest::getVar("sn");
            $calibrationInfo = JRequest::getVar("calibrationInfo");
            $note = JRequest::getVar("note");
            $labAssignedId = JRequest::getVar("labAssignedId");

            $equipment->setName($name);
            $equipment->setEquipmentModel(EquipmentModelPeer::find($equipmentModelID));
            $equipment->setNeesOperated($neesOperated);
            $equipment->setSeparateScheduling($separated);
            $equipment->setOwner($owner);
            $equipment->setSerialNumber($sn);
            $equipment->setCommissionDate((empty($commissiondate)) ? null : $commissiondate);
            $equipment->setCalibrationInformation($calibrationInfo);
            $equipment->setNote($note);
            $equipment->setLabAssignedID($labAssignedId);

            //var_dump($parentequipmentid);

            if( $parentequipmentid > -1 )
            {
                $equipment->setParent(EquipmentPeer::find($parentequipmentid));
            }
            else
            {
                $equipment->setMajor(1);
            }

            $equipment->save();

            //Add or edit after logic
            if($equipmentID == -1)
            {
                // The refresh of this page will turn it from an add page to an edit page
                $equipmentID = $equipment->getId();
                JRequest::setVar('equipmentid', $equipmentID);
                $msg = 'Equipment added';
            }
            else
                $msg = 'Equipment updated';

            //**** Update Class Specific attributes
            $previousequipmentclassid= JRequest::getVar('previousequipmentclassid', -1);
            $currentclassid = $equipment->getEquipmentModel()->getEquipmentClass()->getId();

            // Delete if changed, remove old values
            if( ($previousequipmentclassid != -1) && ($previousequipmentclassid <> $currentclassid) )
            {
                $values = EquipmentAttributeValuePeer::findByEquipment($equipment->getId());

                if( count($values) > 0 )
                {
                    foreach( $values as $value )
                        $value->delete();
                }
            }

            // Handle attributes changes.
            $equipmentClass = $equipment->getEquipmentModel()->getEquipmentClass();

            // Only save if not class had changed, if the class changed, then that means all the valid
            // attributes for this piece of equipment won't correspond to the values returned in the form
            // Need a fresh submission of the form without a class change in order to save these values
            if ($equipmentClass && ($previousequipmentclassid == $currentclassid))
            {
                $classAttributes = EquipmentAttributeClassPeer::findByEquipmentClass($equipmentClass->getId());
                if (count($classAttributes) > 0) {
                    $attrmap = $_REQUEST['AttributeMap'];

                    foreach ($classAttributes as $classat) {
                        $attr = $classat->getEquipmentAttribute();

                        $classid = $equipmentClass->getId();
                        $classatid = $classat->getId();
                        $attrid = $attr->getId();

                        if ($attr->getDataType() == 'GROUP') {

                            $children = EquipmentAttributePeer::findByParent($attr->getId());
                            if (count($children) != 0) {
                                continue;
                            }
                            // We're only doing one level of recursion for now.
                            // this can be expanded later if necessary, but don't
                            // expect this to be a very likely feature request.
                            foreach ($children as $child) {
                                $attrid = $child->getId();
                                $array_gid = array($attrmap[$classid][$classatid][$attrid]);
                                //$val = EquipmentAttributeValuePeer::findByEquipmentAndAttributeAndClass($equipment, $child, $classat);
                                foreach ($array_gid[0] as $gid => $val) {
                                    if ($val) {
                                        EquipmentAttributePeer::updateGroupAttribute($attrid, $gid, $val);
                                    }
                                }
                            }
                        } else {
                            $val = EquipmentAttributeValuePeer::findByEquipmentAndAttributeAndClass($equipment->getId(), $attr->getId(), $classat->getId());
                            if (count($val) == 0) {
                                $val = new EquipmentAttributeValue($equipment, $classat, $attr);
                            }
                            $val->setValue($attrmap[$classid][$classatid][$attrid]);
                            $val->save();
                        }
                    }
                }
            } // end if for class attribute saves
        }// end if for equipment save

        // if error, send all the previously submitted form values to the form doesn't get cleared out
        if(!empty($errorMsg))
        {
            //JRequest::setVar('Name', $values['Name']) = JRequest::getVar('Name', $values['Name']);
        }


        JRequest::setVar('msg', $msg);
        JRequest::setVar('errorMsg', $errorMsg);
        JRequest::setVar('id', $facilityID);
        Jrequest::setVar('equipmentid', $equipmentID);
        JRequest::setVar('errorMsg', $errorMsg);

        JRequest::setVar('view','editequipment');
        parent::display();

    }

    
    function savesensormodel()
    {
        $msg = '';
        $errorMsg = '';

        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canEdit = FacilityHelper::canEditSensorModel($facility);
        $canCreate = FacilityHelper::canCreateSensorModel($facility);
        $sensorModelID = JRequest::getVar('sensormodelid', -1);

        // Check for cancel button click
        $submitbutton = JRequest::getVar('submitbutton', '');
        if($submitbutton == 'Cancel')
        {
            JRequest::setVar('view','sensors');
            $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=sensors', false));
            $this->redirect();
            return;
        }


        // These two errors are hard enough to warrant 500 codes, no legit reason to get here unless
        // you are hacking or a major problem has occured
        if(!$canEdit && $sensorModelID > -1)
            return JError::raiseError( 500, "You do not have access to edit sensors for this facility");

        if(!$canCreate  && $sensorModelID == -1)
            return JError::raiseError( 500, "You do not have access to add sensors for this facility");

        /* @var $sensorModel SensorModel */
        if($sensorModelID == -1)
            $sensorModel = new SensorModel();
        else
            $sensorModel = SensorModelPeer::find($sensorModelID);
        
        //Grab the form fields
        $propertyNames = SensorModelPeer::getFieldNames(BasePeer::TYPE_PHPNAME);

        foreach ($propertyNames as $propName)
        {
            // Do not set the Id
            if($propName == 'Id') continue;

            $propValue = JRequest::getVar($propName, '');
            $values[$propName] = $propValue;
            $sensorModel->setByName($propName, $propValue);
        }

        // Validation
        $name = $sensorModel->getName();
        if (empty($name))
            $errorMsg .=  ( (!empty($errorMsg)) ? "<br/>" : '' ) . "Name is a required field";

        // Only save if we are error free
        if ( empty($errorMsg) )
        {
            $sensorModel->save();

            if($sensorModelID == -1)
            {
                $msg = 'Sensor successfully added';

                // The refresh of this page will turn it from an add page to an edit page
                $sensorModelID = $sensorModel->getId();
                JRequest::setVar('sensorModelID', $sensorModelID);

            }
            else
                $msg = 'Sensor successfully updated';
        }

        // if error, send all the previously submitted form values to the form doesn't get cleared out
        if(!empty($errorMsg))
        {
            JRequest::setVar('Name', $values['Name']);
            JRequest::setVar('SensorTypeId', $values['SensorTypeId']);
            JRequest::setVar('Manufacturer', $values['Manufacturer']);
            JRequest::setVar('Model', $values['Model']);
            JRequest::setVar('Description', $values['Description']);
            JRequest::setVar('SignalType', $values['SignalType']);
            JRequest::setVar('MinMeasuredValue', $values['MinMeasuredValue']);
            JRequest::setVar('MaxMeasuredValue', $values['MaxMeasuredValue']);
            JRequest::setVar('MeasuredValueUnitsId', $values['MeasuredValueUnitsId']);
            JRequest::setVar('Sensitivity',  $values['Sensitivity']);
            JRequest::setVar('SensitivityUnitsId', $values['SensitivityUnitsId']);
            JRequest::setVar('MinOpTemp', $values['MinOpTemp']);
            JRequest::setVar('MaxOpTemp', $values['MaxOpTemp']);
            JRequest::setVar('TempUnitsId', $values['TempUnitsId']);
            JRequest::setVar('Note', $values['Note']);
        }

        JRequest::setVar('msg', $msg);
        JRequest::setVar('errorMsg', $errorMsg);

        //JRequest::setVar('view','editsensormodel');
        //parent::display();

        $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=editsensormodel&sensormodelid=' . $sensorModelID . '&msg=' . $msg . '&errorMsg=' . $errorMsg, false));
        $this->redirect();

    }



    //for editing or creating
    function savesensor()
    {
        $msg = '';
        $errorMsg = '';

        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canEdit = FacilityHelper::canEdit($facility);
        $canCreate = FacilityHelper::canCreate($facility);
        $sensorID = JRequest::getVar('sensorid', -1);

        // These two errors are hard enough to warrant 500 codes, no legit reason to get here unless
        // you are hacking or a major problem has occured
        if(!$canEdit && $sensorid > -1)
            return JError::raiseError( 500, "You do not have access to edit sensors for this facility");

        if(!$canCreate  && $sensorid == -1)
            return JError::raiseError( 500, "You do not have access to add sensors for this facility");


        $serialNumber = trim(JRequest::getVar("serialNumber"));
        $localId = trim(JRequest::getVar("localId"));
        $supplier = trim(JRequest::getVar("supplier"));
        $commissionDate = FacilityHelper::changeDateFormat(trim(JRequest::getVar('commissionDate')));
        $decommissionDate = FacilityHelper::changeDateFormat(trim(JRequest::getVar('decommissionDate')));
        $name = trim(JRequest::getVar('name'));
        $sensorModelId = JRequest::getVar('sensorModelId');
        $originalName = JRequest::getVar('originalName');

        // used to check for unique sensor name later
        $sensorNames = $this->getSensorNames($facility);

        if (empty($name))
        {
            $errorMsg .=  ( (!empty($errorMsg)) ? "<br/>" : '' ) . "Sensor name is empty";
        }

        if (($name != $originalName) && array_key_exists($name, $sensorNames ))
        {
            $errorMsg .= ( (!empty($errorMsg)) ? "<br/>" : '' ) . "Sensor name is not unique, please specify another name";
        }

        if(is_null($sensorModelId))
        {
            $errorMsg .= ( (!empty($errorMsg)) ? "<br/>" : '' ) . "No sensor model was selected";
        }


        // No errors? Do the update/add
        if ( empty($errorMsg) )
        {

            $sensorModel = SensorModelPeer::find($sensorModelId);
            if(is_null($sensorModel))
            {
                $errorMsg = "Could not find the sensor model for this sensor";
            }
            else
            {
                if($sensorID == -1) //add
                {

                    $sensor = new Sensor($sensorModel,
                            $name,
                            $serialNumber,
                            $localId,
                            $supplier,
                            $commissionDate,
                            $decommissionDate,
                            false);

                    $sensor->save();
                    $sensor->addToFacility( $facility );

                    $msg = 'Sensor successfully added';

                }
                else // edit
                {
                    $sensor = SensorPeer::find($sensorID);

                    $sensor->setSensorModel($sensorModel);
                    $sensor->setName($name);
                    $sensor->setSerialNumber($serialNumber);
                    $sensor->setLocalId($localId);
                    $sensor->setSupplier($supplier);
                    $sensor->setCommissionDate($commissionDate);
                    $sensor->setDecommissionDate($decommissionDate);
                    $sensor->setDeleted(0);
                    $sensor->save();

                    $msg = 'Sensor successfully updated';
                }
            }
        }// end if

        JRequest::setVar('msg', $msg);
        JRequest::setVar('errorMsg', $errorMsg);

        JRequest::setVar('view','editsensor');
        parent::display();

       $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=editsensor&sensorid= ' . $sensor->getId() . ' &msg=' . $msg . '&errorMsg=' . $errorMsg, false));
       $this->redirect();


    }


    function deletesitemembership()
    {
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canGrant = FacilityHelper::canGrant($facility);
        $editpersonid = JRequest::getVar('editpersonid', '-1');
        
        if($editpersonid == -1)
            return JError::raiseError( 500, "No personid available");

        if(!$canGrant)
            return JError::raiseError( 500, "You do not have access to grant or edit facility members");

        $editPerson = PersonPeer::find($editpersonid);
        $editPerson->removeFromEntity($facility);

        $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=staff'));
    }




    function addsitemembership()
    {
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canGrant = FacilityHelper::canGrant($facility);
        $editpersonid = JRequest::getVar('editpersonid', '-1');

        if(!$canGrant)
        {
            return JError::raiseError( 500, "You do not have access to grant or edit facility members" );
        }

        $editPerson = PersonPeer::find($editpersonid);

        if(!$editPerson)
            return JError::raiseError( 500, "Cannot locate PersonPeer record with ID of: " . $editperonsid );

        $editPersonId = $editPerson->getId();

        $editPerson->removeFromEntity($facility);

        $perms = new Permissions(Permissions::PERMISSION_VIEW);
        $auth = new Authorization($editpersonid, $facilityID, DomainEntityType::ENTITY_TYPE_FACILITY, $perms);
        $auth->save();

        $this->_redirect =  base64_encode(JRoute::_('index.php?option=com_sites&id=' . $facilityID . '&view=staff'));
    }


    /*
     * Used as a form target to delte files within the site
     */
    function deletefile()
    {
        $redirectURL = JRequest::getVar('redirectURL');
        $returnView = JRequest::getVar('redirectView');
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);

        // See if the current user can delete files for this facility
        $canDelete = FacilityHelper::canDelete($facility);

        if(!$canDelete)
        {
            return JError::raiseError( 500, "You do not have access to delete files" );
        }

        $fileId =  JRequest::getVar('fileid');
        $df = DataFilePeer::find($fileId);

        if(!$df)
        {
            return JError::raiseError( 500, "Error: data file not dound in database." );
        }

        $df->fullDeleteSingleFile();
        
        $this->_redirect = $redirectURL;
    }


    function savefile()
    {
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);

        // These three are used to construct
        $infotype = JRequest::getVar('infotype');
        $subinfo = JRequest::getVar('subinfo');
        $groupby = JRequest::getVar('groupby');

        // get current user
        $user =& JFactory::getUser();

        $msg = '';
        $errorMsg = '';

        $can_edit = FacilityHelper::canCreate($facility);

        if(!$can_edit)
        {
            $errorMsg = "You do not have permission to create files for this facility";
        }
        else
        {
            //Lets do the actual addition of this file

            // This seems like a hack to me (DRB 7/2/2010)
            if ( $infotype == "VisitorInformation"){
                //  for Contact tab but with visitorinfo
                $dir_infotype = "Contact";
            }
            else
            {
                $dir_infotype = $infotype;
            }

            $dest = $facility->getPathname() . "/".$dir_infotype;

            if($subinfo)
            {
                $sub = str_replace(" ",  "",  $subinfo );
                $dest = $dest . "/" . $sub ;
            }

            //echo $dest;
            //echo '<br/><br/><br/>';
            //var_dump($_FILES);

            # See if we're uploading a document
            if( $_FILES["documentFile"] && $_FILES["documentFile"]['name'] )
            {
                $newDataFile = DataFile::newDataFileByUpload($_FILES["documentFile"], $dest);

                // Handle new uploads.
                if ($newDataFile)
                {
                    $fac_datafile = new FacilityDataFile($facility, $newDataFile, $infotype, $subinfo, $groupby);
                    $fac_datafile->save();
                }
            }


            // no matter it is a new or old data file, update its title and desc
            if($fac_datafile)
            {
                $datafile = $fac_datafile->getDataFile();
                $datafile->setTitle(JRequest::getVar('documentTitle'));
                $datafile->setDescription(JRequest::getVar('documentDesc'));
                $datafile->save();
            }

        }

        JRequest::setVar('msg', $msg);
        JRequest::setVar('errorMsg', $errorMsg);
        
        $redirectURL = JRequest::getVar('redirectURL');

        //echo $dest;
        //echo '<br>';
        //echo $redirectURL;
        //return;

        $this->_redirect = $redirectURL;

    }



    /*
     *
     * Edit Site main tab form directs here to actually perform the site update
     *
     */
    function savesite()
    {
        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);

        $canEdit = FacilityHelper::canEdit($facility);

        if(!$canEdit)
        {
            return JError::raiseError( 500, "You do not have access to edit this facility" );
        }

        $msg = '';
        $errorMsg = '';

        // Validation
        if ( strlen( trim(JRequest::getVar('siteName')) ) < 1) {
            $errorMsg = 'Please enter a valid Facility Name for the facility.';
        }
        else
        {
            $facility->setSiteName(JRequest::getVar('siteName'));
            $facility->setDepartment(JRequest::getVar('department'));
            $facility->setLaboratory(JRequest::getVar('laboratory'));
            $facility->setUrl(JRequest::getVar('website_URL'));
            $facility->setDescription(JRequest::getVar('description'));
            $facility->setNsfAwardUrl(JRequest::getVar('nsfAward_URL'));
            $facility->setNsfAcknowledgement(JRequest::getVar('nsfAcknowledgement'));

            $facility->save();
            $msg = "Site sucessfully updated";
        }

        JRequest::setVar('msg', $msg);
        JRequest::setVar('errorMsg', $errorMsg);

        JRequest::setVar('view','editsite');
        parent::display();

    }


    /*
     *
     * Save task for the edit contact roles and permissions page
     *
     */
    function savecontactrolesandpermissions()
    {
        // Grab everything from the form
        $facilityID = JRequest::getVar('facilityID');
        $editPersonID = JRequest::getVar('editpersonid');
        $roleIds = JRequest::getVar('roleIds');
        $facility = FacilityPeer::find($facilityID);

        // This tracks if the current user has the ability to grant roles/permissions
        // for the Facility. It is not the specified value form value that sets the
        // canGrant value for the user being edited
        $canAdminGrant = FacilityHelper::canGrant($facility);

        if(!$canAdminGrant)
        {
            return JError::raiseError( 500, "You do not have access to grant or edit facility members" );
        }

        $canEdit = JRequest::getVar("canEdit");
        $canDelete = JRequest::getVar("canDelete");
        $canCreate = JRequest::getVar("canCreate");
        $canGrant = JRequest::getVar("canGrant");

        $msg = '';
        $errorMsg = '';

        // get current user
        $user =& JFactory::getUser();

        FacilityHelper::savecontactrolesandpermissions($facilityID,
                $user->username,
                $editPersonID,
                $roleIds,
                $canEdit,
                $canDelete,
                $canCreate,
                $canGrant,
                &$msg,
                &$errorMsg);

        JRequest::setVar('msg', $msg);
        JRequest::setVar('errorMsg', $errorMsg);

        JRequest::setVar('view','editcontactrolesandpermissions');
        parent::display();
    }




    public function redirect()
    {
        if ($this->_redirect != NULL) {

            // Just make sure you base64_encode redirect URLs
            $this->_redirect = base64_decode($this->_redirect);

            $app =& JFactory::getApplication();
            $app->redirect( $this->_redirect, $this->_message, $this->_messageType );
        }
    }


    function getSensorNames($facility)
    {
        $names = array();
        $sensors = $facility->getSensors();
        $i = 0;
        foreach($sensors as $sensor)
        {
            $names[$sensor->getName()] = $i++;
        }
        return $names;
    }
	
	
    function display()
    {
    	parent::display();
    }
	

    function editsite()
    {
            JRequest::setVar('view','editsite');
            parent::display();
    }

    
    function editcontactperson()
    {
            JRequest::setVar('view','editcontactperson');
            parent::display();
    }



    /*
     * Checks date is in m-d-yyyy format
     *
     * &$oracleFormat is a shortcut that returns the formatted date properly
     * formatted for an oracle format (yyyy-d-m). It is only defined if the
     * passed in date was properly formatted 
     * 
     */
    function validateDateTime($testDt, &$oracleFormat)
    {
        $rv = false;
        //$matches_dt = '';

        if (empty($testDt))
            $rv = false;
        else
        {
            // Parse the date
            preg_match('/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/', trim($testDt), $matches_dt);

            if ((count($matches_dt) == 4) && (checkdate($matches_dt[1], $matches_dt[2], $matches_dt[3])))
                $rv = true;
            else
                $rv = false;
        }

        // Only return a formatted date if the passed in date was valid
        if($rv)
            $oracleFormat = $matches_dt[3] . "-" . $matches_dt[1] . "-" . $matches_dt[2];

        return $rv;
    }

}
