<?php
JLoader::import('Hubzero.Api.Controller');

class EventsApiController extends Hubzero_Api_Controller
{
	function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');
		
		switch($this->segments[0]) 
		{
			case 'latest':		$this->index();		break;
			default:			$this->index();
		}
	}
	
	
	private function not_found()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(404,'Not Found');
	}
	
	
	function index()
	{
		//get the userid
		$userid = JFactory::getApplication()->getAuthn('user_id');
		
		//if we dont have a user return nothing
		if ($userid == null)
		{
			return $this->not_found();
		}
		
		//get the request vars
		$limit = JRequest::getVar("limit", 5);
		
		//load up the
		$database =& JFactory::getDBO();
		$query = "SELECT * FROM #__events as e 
					WHERE publish_up <= NOW() 
					AND publish_down >= NOW()
					AND state=1 
					AND approved=1
					LIMIT {$limit}";
					
		//sleep(10);
					
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		
		$obj = new stdClass();
		$obj->events = $rows;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}
	

}
