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

//require_once "bulkupload/FileUploadReader.php";
//require_once "lib/data/Calibration.php";


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
        $this->registerTask( 'douploadsensorcalibration' , 'douploadsensorcalibration' );

        $this->registerTask('savecontactrolesandpermissions' , 'savecontactrolesandpermissions');
        $this->registerTask('savesite' , 'savesite');
        $this->registerTask('savefile' , 'savefile');
        $this->registerTask('deletefle', 'deletefile');
        $this->registerTask('addsitemembership', 'addsitemembership');
        $this->registerTask('deletesitemembership', 'deletesitemembership');
        $this->registerTask('editsensor', 'editsensor');

    }


    //for editing or creating
    function editsensor()
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


        }


        JRequest::setVar('msg', $msg);
        JRequest::setVar('errorMsg', $errorMsg);

        JRequest::setVar('view','editsensor');
        parent::display();

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

        // show the staff page
        JRequest::setVar('view','staff');
        parent::display();


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

        // show the staff page
        JRequest::setVar('view','editcontactrolesandpermissions');
        parent::display();

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


            echo $dest;

            echo '<br/><br/><br/>';

            var_dump($_FILES);


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

        JRequest::setVar('view','staff');
        parent::display();
    }



    /*******************************************************************************
     *
     *
     *******************************************************************************/
    function douploadsensorcalibration()
    {

            if(!empty($_FILES["uploadFile"]))
            {

                    $newFilePath = "/tmp/" . time() . "_" . $_FILES["uploadFile"]["name"];
                    move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $newFilePath);
            }

        $reader = new FileUploadReader($newFilePath);

        $cells = $reader->getData();

        if(!is_array($cells)) {
          $this->setAlertmsg("Unable to parse your uploaded data file. Make sure you are uploading a valid document.<br/>Remember, the accepted formats are Excel (95, 97, 2000, 2003) (*.xls), and comma-delimited text files (*.csv), and tab-delimited text files (*.txt), and XML Speadsheet (*.xml)");
          return;
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

            $msg = "Row does not have correct number of columns. <br/>These columns must be: " . implode(", ", $columns) . " <br/>and must be in the same of this order";

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



    // ***********************************************************************************************************
    //Helper functions



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

  
  
  
  
  
  
}
