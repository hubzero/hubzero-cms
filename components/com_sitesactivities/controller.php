<?php
/**
 * Primary controller file for the siteactivities component 
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
class SitesActivitiesController extends JController
{

	function __construct()
	{
		parent::__construct();
	}	
	
	
	
    function display()
    {
    	parent::display();
    }
  
}
