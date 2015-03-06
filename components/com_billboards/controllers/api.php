<?php
JLoader::import('Hubzero.Api.Controller');

class BillboardsControllerApi extends \Hubzero\Component\ApiController
{
	function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		switch ($this->segments[0])
		{
			case 'index':       $this->index();      break;
			case 'collection':  $this->collection(); break;
			default:            $this->index();
		}
	}

	private function not_found()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(404, 'Not Found');
	}

	private function index()
	{
		// If we dont have a user, return an error
		if (JFactory::getApplication()->getAuthn('user_id') == null)
		{
			return $this->not_found();
		}

		// Get the request vars
		$limit      = JRequest::getVar("limit", 25);
		$limitstart = JRequest::getVar("limitstart", 0);

		// Load up the entries
		require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_billboards' . DS . 'models' . DS . 'collection.php';
		$collections = Collection::start($limitstart)->limit($limit)->rows();

		$this->setMessageType("application/json");
		$this->setMessage(array('collections' => $collections->toArray()));
	}

	private function collection()
	{
		// If we dont have a user, return an error
		if (JFactory::getApplication()->getAuthn('user_id') == null)
		{
			return $this->not_found();
		}

		// Get the collection id
		$collection = 0;
		if (isset($this->segments[1]))
		{
			$collection = $this->segments[1];
		}
		$collection = JRequest::getVar("collection", $collection);

		// Load up the collection
		require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_billboards' . DS . 'models' . DS . 'collection.php';
		require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_billboards' . DS . 'models' . DS . 'billboard.php';
		$collection = Collection::oneOrNew($collection);

		// Make sure we found a collection
		if ($collection->isNew())
		{
			return $this->not_found();
		}

		$billboards = $collection->billboards()
		                         ->select('id')
		                         ->select('name')
		                         ->select('learn_more_target')
		                         ->select('background_img')
		                         ->whereEquals('published', 1)
		                         ->rows();

		foreach ($billboards as $billboard)
		{
			$image = $billboard->get('background_img');
			$billboard->set('retina_background_img', $image);

			if (is_file(JPATH_ROOT . DS . $image))
			{
				$image_info   = pathinfo($image);
				$retina_image = $image_info['dirname'] . DS . $image_info['filename'] . "@2x." . $image_info['extension'];
				if (file_exists(JPATH_ROOT . DS . $retina_image))
				{
					$billboard->set('retina_background_img', $retina_image);
				}
			}
		}

		// Get the collection and its billboards
		$collection = array(
			'id'         => $collection->id,
			'name'       => $collection->name,
			'billboards' => $billboards->toArray()
		);

		$this->setMessageType("application/json");
		$this->setMessage(array('collection' => $collection));
	}
}
