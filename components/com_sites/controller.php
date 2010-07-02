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

		$this->registerTask( 'savecontactrolesandpermissions' , 'savecontactrolesandpermissions' );
		$this->registerTask( 'savesite' , 'savesite' );
		$this->registerTask( 'savefile' , 'savefile' );
		
	}	
	
	
	
	function savefile()
	{
		
		$facilityID = JRequest::getVar('facilityID');
		
		// get current user
		$user =& JFactory::getUser();
		
		$msg = '';
		$errorMsg = '';
		
		$can_edit = FacilityHelper::canEdit($facility);
		
		if(!$can_edit) 
		{
			$errorMsg = "You do not have permission to edit files for this facility";
		}
		else
		{
			
			
			
		}

		
		
		JRequest::setVar('msg', $msg);
		JRequest::setVar('errorMsg', $errorMsg);
				
		JRequest::setVar('view','editsite');
		parent::display();


	}
	
	
	
	/*
	 * 
	 * Edit Site main tab form directs here to actually perform the site update
	 * 
	 */
	function savesite()
	{
	
		$msg = '';
		$errorMsg = '';
		
		$facilityID = JRequest::getVar('id');
    	$facility = FacilityPeer::find($facilityID);
		
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
                        //$facility->se

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
	
	
	
	
	
	
	
	
	
	
	/*******************************************************************************
	 * 
	 * 
	 *******************************************************************************/
	function douploadsensorcalibration()
	{
		
		if(!empty($_FILES["uploadFile"])) {

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
