<?php
JLoader::import('Hubzero.Api.Controller');

class NewsApiController extends Hubzero_Api_Controller
{
	function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		switch($this->segments[0]) 
		{
			case 'news':		$this->index();		break;
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
		$limit 		= JRequest::getVar("limit", 5);
		$section 	= JRequest::getVar("section", 'news');
		$category 	= JRequest::getVar("category", 'latest');
		
		//load up the
		$database =& JFactory::getDBO();
		$query = "SELECT c.* FROM #__content as c, #__sections as s, #__categories as cat 
					WHERE s.alias='{$section}' 
					AND s.id=c.sectionid 
					AND cat.alias='{$category}' 
					AND cat.section=s.id 
					AND state=1
					ORDER BY c.ordering ASC
					LIMIT {$limit}";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		
		//sleep(15);
		
		$obj = new stdClass();
		$obj->news = $rows;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}
	

}
