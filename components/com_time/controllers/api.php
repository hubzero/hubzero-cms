<?php

class TimeApiController extends Hubzero_Api_Controller
{
	function execute()
	{
		// Import some Joomla libraries
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		// Get the request type
		$this->format = JRequest::getVar('format', 'json');

		// Get a database object
		$this->db = JFactory::getDBO();

		// Import time JTable libraries
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'tasks.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'hubs.php');
		require_once(JPATH_ROOT.DS.'plugins'.DS.'time'.DS.'tables'.DS.'records.php');

		// Establish response variables
		$this->segments = $this->getRouteSegments();
		$this->response = $this->getResponse();

		// Switch based on task (i.e. "/api/time/xxxxx")
		switch($this->segments[0])
		{
			case 'records':  $this->records();             break;
			case 'tasks':    $this->tasks();               break;
			case 'hubs':     $this->hubs();                break;

			default:         $this->method_not_found();    break;
		}
	}

	//--------------------------
	// Records function
	//--------------------------

	function records()
	{
		// Incoming posted data
		$pid       = JRequest::getInt('pid', 0);
		$startdate = JRequest::getVar('startdate', '2000-01-01');
		$enddate   = JRequest::getVar('enddate', '2100-01-01');
		$limit     = JRequest::getInt('limit', 100);
		$start     = JRequest::getInt('start', 0);

		// Filters for query
		$filters = array('limit'=>$limit, 'start'=>$start, 'pid'=>$pid, 'startdate'=>$startdate, 'enddate'=>$enddate);

		// Create object and get records
		$record  = new TimeRecords($this->db);
		$records = $record->getRecords($filters);

		// Create object with records property
		$obj = new stdClass();
		$obj->records = $records;

		// Return object
		$this->response->setResponseProvides($this->format);
		$this->response->setMessage($obj);
	}

	//--------------------------
	// Tasks function
	//--------------------------

	function tasks()
	{
		// Incoming posted data
		$hub_id  = JRequest::getInt('hid', 0);
		$pactive = JRequest::getInt('pactive', 1);
		$limit   = JRequest::getInt('limit', 100);
		$start   = JRequest::getInt('start', 0);

		// Filters for the query
		$filters = array('limit'=>$limit, 'start'=>$start, 'hub'=>$hub_id, 'active'=>$pactive);

		// Get list of tasks
		$taskObj = new TimeTasks($this->db);
		$tasks   = $taskObj->getTasks($filters);

		// Create object with tasks property
		$obj = new stdClass();
		$obj->tasks = $tasks;

		// Return object
		$this->response->setResponseProvides($this->format);
		$this->response->setMessage($obj);
	}

	//--------------------------
	// Hubs function
	//--------------------------

	function hubs()
	{
		// Incoming posted data
		$active = JRequest::getInt('active', 1);
		$limit  = JRequest::getInt('limit', 100);
		$start  = JRequest::getInt('start', 0);

		// Filters for the query
		$filters = array('limit'=>$limit, 'start'=>$start, 'active'=>$active);

		// Get list of hubs
		$hub  = new TimeHubs($this->db);
		$hubs = $hub->getRecords($filters);

		// Create object with tasks property
		$obj = new stdClass();
		$obj->hubs = $hubs;

		// Return object
		$this->response->setResponseProvides($this->format);
		$this->response->setMessage($obj);
	}

	//--------------------------
	// Method not found
	//--------------------------

	function method_not_found()
	{
		// Set the error message
		$message = "Method not found";

		// Create object with error property
		$obj = new stdClass();
		$obj->error = $message;

		// Return object
		$this->response->setResponseProvides($this->format);
		$this->response->setMessage($obj);
	}
}
?>