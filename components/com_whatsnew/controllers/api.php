<?php
JLoader::import('Hubzero.Api.Controller');

class WhatsnewControllerApi extends Hubzero_Api_Controller
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
		$period = JRequest::getVar("period", "year");
		$category = JRequest::getVar("category", "all");
		$limit = JRequest::getVar("limit", 25);
		$limitstart = JRequest::getVar("limitstart", 0);
		$content = JRequest::getVar("content", 0);
		$order = JRequest::getVar("order", "desc");
		
		//import joomla plugin helper
		jimport('joomla.plugin.helper');
		
		// Load plugins
		JPluginHelper::importPlugin('whatsnew');
		$dispatcher =& JDispatcher::getInstance();
		
		//get the search areas
		$areas = array();
		$searchareas = $dispatcher->trigger('onWhatsNewAreas');
		foreach($searchareas as $area)
		{
			$areas = array_merge($areas, $area);
		}
		
		//parse our categories
		//make sure we have a category
		$category = ($category == '') ? 'all' : $category;
		$category = array_filter(array_values(explode(',', $category)));
		
		//if we have an array of categories lets remove any areas not passed in
		if(!in_array('all', $category))
		{
			foreach($areas as $k => $area)
			{
				if(!in_array($k, $category))
				{
					unset($areas[$k]);
				}
			}
		}
		
		//parse the period
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_whatsnew' . DS . 'helpers' . DS . 'period.php');
		$p = new WhatsnewPeriod( $period );
		$p->process();
		
		$results = $dispatcher->trigger(
			'onWhatsnew', 
			array(
				$p,
				999,
				0,
				$areas
			)
		);
		
		$whatsnew = array();
		foreach($results as $results_section)
		{
			foreach($results_section as $result)
			{
				$item = array();
				$item['title'] = stripslashes($result->title);
				$item['link'] = $result->href;
				$item['date'] = @$result->created;
				switch($result->section)
				{
					case "resources":	$item['section'] = stripslashes($result->area);			break;
					case "content":		$item['section'] = "content articles";					break;
					default:			$item['section'] = stripslashes($result->section);		break;
				}
				if($content)
					$item['text'] = $result->text;
				$whatsnew[] = $item;
			}
		}
		
		//order by the date created
		if($order == 'asc')
		{
			usort($whatsnew, array("WhatsnewControllerApi", "sorter_asc"));
		}
		else
		{
			usort($whatsnew, array("WhatsnewControllerApi", "sorter"));
		}
		
		//
		$w = array();
		$count = 0;
		for($i=$limitstart, $n=count($whatsnew); $i<$n; $i++)
		{
			if($count < $limit)
			{
				$w[] = $whatsnew[$i];
			}
			$count++;
		}

		$obj = new stdClass();
		$obj->whatsnew = $w;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}
	
	function sorter($a, $b)
	{	
		return (strtotime($a['date']) < strtotime($b['date'])) ? 1 : -1;
	}
	
	function sorter_asc($a, $b)
	{	
		return (strtotime($a['date']) < strtotime($b['date'])) ? -1 : 1;
	}
	
}
