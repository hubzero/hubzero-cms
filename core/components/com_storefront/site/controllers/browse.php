<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Storefront\Site\Controllers;

use Pathway;
use Components\Storefront\Models\Warehouse;

/**
 * Product browsing controller class
 */
class Browse extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->warehouse = new Warehouse();

		$this->warehouse->addAccessLevels(User::getAuthorisedViewLevels());

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
	 * @return     void
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
	 * @param		$cId
	 * @return     	void
	 */
	private function browseCollection($cId)
	{
		$view = new \Hubzero\Component\View(array('name'=>'browse', 'layout' => 'collection') );

		// Get the collection products
		$this->warehouse->addLookupCollection($cId);
		$products = $this->warehouse->getProducts();

		$view->products = $products;

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
			Lang::txt('COM_STOREFRONT_BROWSING_COLLECTION')
		);

		$view->display();
	}

}

