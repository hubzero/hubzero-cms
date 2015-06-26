<?php
JLoader::import('Hubzero.Api.Controller');

class NewsControllerApi extends \Hubzero\Component\ApiController
{
	public function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		switch ($this->segments[0])
		{
			case 'index': $this->index();     break;
			default:      $this->not_found(); break;
		}
	}

	private function not_found()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(404,'Not Found');
	}

	public function index()
	{
		//get the userid
		$userid = JFactory::getApplication()->getAuthn('user_id');

		//if we dont have a user return nothing
		if ($userid == null)
		{
			return $this->not_found();
		}

		//get the request vars
		$limit    = Request::getVar('limit', 5);
		$section  = Request::getVar('section', 'news');
		$category = Request::getVar('category', 'latest');
		$format   = Request::getVar('format', 'json');

		//load up the news articles
		$database = App::get('db');

		$query = "SELECT c.*
					FROM `#__content` as c, `#__categories` as cat
					WHERE cat.alias='{$category}'
					AND c.catid=cat.id
					AND state=1
					ORDER BY c.ordering ASC
					LIMIT {$limit}";

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		//return the results
		$object = new stdClass();
		$object->news = $rows;

		$this->setMessageType($format);
		$this->setMessage($object);
	}
}
