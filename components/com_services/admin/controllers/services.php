<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Services\Admin\Controllers;

use Components\Services\Tables\Service;
use Hubzero\Component\AdminController;

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
		// Get configuration
		$config = \JFactory::getConfig();
		$app = \JFactory::getApplication();

		$this->view->filters = array(
			// Get paging variables
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$config->getValue('config.list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'category'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
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
		$database = \JFactory::getDBO();

		$objS = new Service($database);
		$now = \JFactory::getDate()->toSql();

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
		\JRequest::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = \JRequest::getVar('id', array(0));
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
	 * @return	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = \JRequest::getVar('fields', array(), 'post');
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
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}
}

