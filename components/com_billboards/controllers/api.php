<?php
JLoader::import('Hubzero.Api.Controller');

class BillboardsControllerApi extends \Hubzero\Component\ApiController
{
	function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		switch($this->segments[0])
		{
			case 'index':		$this->index();			break;
			case 'collection':	$this->collection();	break;
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
		$limit = JRequest::getVar("limit", 25);
		$limitstart = JRequest::getVar("limitstart", 0);

		//load up the
		$database = JFactory::getDBO();
		require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_billboards' . DS . 'tables' . DS . 'collection.php' );
		$BillboardsCollections = new BillboardsCollection( $database );
		$collections = $BillboardsCollections->getRecords( array("limit" => $limit, "start" => $limitstart) );

		$obj = new stdClass();
		$obj->collections = $collections;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}

	function collection()
	{
		//get the userid
		$userid = JFactory::getApplication()->getAuthn('user_id');

		//if we dont have a user return nothing
		if ($userid == null)
		{
			return $this->not_found();
		}

		$collection = 0;
		if(isset($this->segments[1]))
		{
			$collection = $this->segments[1];
		}
		$collection_query = JRequest::getVar("collection");
		if(isset($collection_query))
		{
			$collection = $collection_query;
		}

		//load up the
		$database = JFactory::getDBO();
		require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_billboards' . DS . 'tables' . DS . 'collection.php' );
		$BillboardsCollections = new BillboardsCollection( $database );
		$BillboardsCollections->load($collection);

		//make sure we found a collection
		if(!$BillboardsCollections->name)
		{
			return $this->not_found();
		}

		//get the collection and its billboards
		$collection = array(
			'id' => $BillboardsCollections->id,
			'name' => $BillboardsCollections->name,
			'billboards' => $BillboardsCollections->getBillboards( array("collection"=>$BillboardsCollections->id, "published"=>1, "include_retina" => true) )
		);

		$obj = new stdClass();
		$obj->collection = $collection;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}
}
