<?php

class ResourcesApiController extends Hubzero_Api_Controller
{
	function execute()
	{
		ini_set('display_errors', 1); 
		error_reporting(E_ALL);
		
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');
		
		$this->segments = $this->getRouteSegments();
		$this->response = $this->getResponse();
		
		switch($this->segments[0]) 
		{
			case 'whatsnew':		$this->whatsNew();					break;
			default:				$this->no_matching_method();		break;
		}
	}
	
	//-----
	
	function whatsNew()
	{
		//get request vars
		$format = JRequest::getVar('format', 'json');
		$limit = JRequest::getVar('limit', 25);
		$period = JRequest::getVar('period', 'month');
		$category = JRequest::getVar('category', '');
		
		ximport('Hubzero_Whatsnew');
		JLoader::import('joomla.plugin.helper');
		
		$whatsnew = Hubzero_Whatsnew::getWhatsNewBasedOnPeriodAndCategory( $period, $category, $limit );
		
		//encode results and return response
		$object = new stdClass();
		$object->whatsnew = $whatsnew;
		$this->response->setResponseProvides( $format );
		$this->response->setMessage( $object );
	}
}
?>