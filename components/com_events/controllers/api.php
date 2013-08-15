<?php
JLoader::import('Hubzero.Api.Controller');

class EventsControllerApi extends Hubzero_Api_Controller
{
	function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');
		
		switch($this->segments[0]) 
		{
			case 'index':		$this->index();			break;
			default:			$this->not_found();
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
		$limit 	= JRequest::getVar('limit', 5);
		$format = JRequest::getVar('format', 'json');
		
		//load up the events
		$database =& JFactory::getDBO();
		$query = "SELECT * FROM #__events as e 
					/* WHERE publish_up <= NOW() */
					WHERE publish_down >= NOW()
					AND state=1 
					AND approved=1
					AND scope='event'
					LIMIT {$limit}";
					
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		
		//return results
		$object = new stdClass();
		$object->events = $rows;
		$this->setMessageType( $format );
		$this->setMessage( $object );
	}
	

}
