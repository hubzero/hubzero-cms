<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
class Search extends \Hubzero\Component\SiteController
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
			$this->_task = 'search';
			$this->registerTask('__default', $this->_task);
		}

		parent::execute();
	}

	/**
	 * Display default page
	 *
	 * @return  void
	 */
	public function searchTask()
	{
		// Incoming
		$search = Request::getString('q', '');
		$cId = Request::getString('cId', '');

		if ($cId)
		{
			$this->warehouse->addLookupCollection($cId);
		}

		$products = $this->warehouse->getProducts('rows', true, array('sort' => 'title', 'search' => $search));
		$this->view->products = $products;
		$this->view->config = $this->config;
		$this->view->search = $search;

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}

		Pathway::append(
			Lang::txt('Search')
		);

		$this->view->display();
	}
}
