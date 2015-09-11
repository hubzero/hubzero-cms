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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Admin\Controllers;

use Components\Services\Tables\Service;
use Hubzero\Component\AdminController;
use Request;
use Config;
use Route;
use Lang;
use Date;
use App;

/**
 * Controller class for services
 */
class Services extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Services List
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view->filters = array(
			// Get paging variables
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'category'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// get all available services
		$objS = new Service($this->database);
		$this->view->rows = $objS->getServices('', 1, '', $this->view->filters['sort'], $this->view->filters['sort_Dir'], '', 1);

		$this->view->total = ($this->view->rows) ? count($this->view->rows) : 0;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Initial setup of default jobs services
	 *
	 * @return  boolean
	 */
	protected function setupServices()
	{
		$database = \App::get('db');

		$objS = new Service($database);
		$now = Date::toSql();

		$default1 = array(
			'id' => 0,
			'title' => Lang::txt('COM_SERVICES_BASIC_SERVICE_TITLE'),
			'category' => strtolower(Lang::txt('COM_SERVICES_JOBS')),
			'alias' => 'employer_basic',
			'status' => 1,
			'description' => Lang::txt('COM_SERVICES_BASIC_SERVICE_DESC'),
			'unitprice' => '0.00',
			'pointprice' => 0,
			'currency' => '$',
			'maxunits' => 6,
			'minunits' => 1,
			'unitsize' => 1,
			'unitmeasure' => strtolower(Lang::txt('month')),
			'changed' => $now,
			'params' => "promo=" . Lang::txt('COM_SERVICES_BASIC_SERVICE_PROMO') . "\npromomaxunits=3\nmaxads=1"
		);
		$default2 = array(
			'id' => 0,
			'title' => Lang::txt('COM_SERVICES_PREMIUM_SERVICE_TITLE'),
			'category' => strtolower(Lang::txt('COM_SERVICES_JOBS')),
			'alias' => 'employer_premium',
			'status' => 0,
			'description' => Lang::txt('COM_SERVICES_PREMIUM_SERVICE_DESC'),
			'unitprice' => '500.00',
			'pointprice' => 0,
			'currency' => '$',
			'maxunits' => 6,
			'minunits' => 1,
			'unitsize' => 1,
			'unitmeasure' => strtolower(Lang::txt('month')),
			'changed' => $now,
			'params' => "promo=\npromomaxunits=\nmaxads=3"
		);

		if (!$objS->bind($default1))
		{
			$this->setError($objS->getError());
			return false;
		}
		if (!$objS->store())
		{
			$this->setError($objS->getError());
			return false;
		}
		if (!$objS->bind($default2))
		{
			$this->setError($objS->getError());
			return false;
		}
		if (!$objS->store())
		{
			$this->setError($objS->getError());
			return false;
		}
	}

	/**
	 * Edit an entry
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = Request::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			// load infor from database
			$row = new Service($this->database);
			$row->load($id);
		}

		$this->view->row = $row;

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view
			->setLayout('edit')
			->display();
	}

	/**
	 * Saves an entry and redirects to listing
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new Service($this->database);
		if (!$row->bind($fields))
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->check())
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->store())
		{
			$this->setMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		$this->setMessage(
			Lang::txt('COM_SERVICES_SAVED'),
			'message'
		);

		if ($this->_task == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}

