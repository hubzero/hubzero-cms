<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Product browsing controller class
 */
class StorefrontControllerBrowse extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		include_once(JPATH_COMPONENT . DS . 'models' . DS . 'Warehouse.php');
		$this->warehouse = new StorefrontModelWarehouse();

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
		Pathway::addItem('Browsing collection', Route::url('index.php?id=' . '5'));

		$view->display();
	}

}

