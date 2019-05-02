<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Site\Controllers;

use Components\Storefront\Models\Warehouse;
use Pathway;
use Request;
use Lang;
use User;
use App;

/**
 * Product browsing controller class
 */
class Browse extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->warehouse = new Warehouse();

		$this->warehouse->addAccessLevels(User::getAuthorisedViewLevels());
		$this->warehouse->addAccessGroups(User::getAuthorisedGroups());

		if (is_numeric(User::get('id')))
		{
			$this->warehouse->addUserScope(User::get('id'));
		}

		// Get the task
		$this->_task  = Request::getCmd('task', '');

		if (empty($this->_task))
		{
			$this->_task = 'home';
			$this->registerTask('__default', $this->_task);
		}

		$executed = false;
		if (!method_exists($this, $this->_task . 'Task'))
		{
			// Try to find a corresponding collection
			$cId = $this->warehouse->collectionExists($this->_task);
			if ($cId)
			{
				// if match is found -- browse collection
				$executed = true;
				$this->browseCollection($cId);
			}
			else
			{
				App::abort(404, Lang::txt('Collection Not Found'));
			}
		}

		if (!$executed)
		{
			parent::execute();
		}
	}

	/**
	 * Display default page
	 *
	 * @return  void
	 */
	public function homeTask()
	{
		// get categories
		$categories = $this->warehouse->getRootCategories();
		$this->view->categories = $categories;

		$this->view->display();
	}

	/**
	 * Display collection
	 *
	 * @param   integer  $cId
	 * @return  void
	 */
	private function browseCollection($cId)
	{
		$view = new \Hubzero\Component\View(array('name'=>'browse', 'layout' => 'collection'));

		// Get collection name
		$collection = $this->warehouse->getCollectionInfo($cId);
		$collectionName = $collection->cName;

		// Get the collection products
		$this->warehouse->addLookupCollection($cId);
		$products = $this->warehouse->getProducts('rows', true, array('sort' => 'title'));

		$view->cId = $cId;
		$view->collectionName = $collectionName;
		$view->products = $products;

		$view->config = $this->config;

		$this->css('storefront.css', 'com_storefront');

		// Breadcrumbs
		//$this->pathway->addItem('Browsing collection', Route::url('index.php?id=' . '5'));

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		Pathway::append(
			Lang::txt($collectionName)
		);

		$view->display();
	}
}
