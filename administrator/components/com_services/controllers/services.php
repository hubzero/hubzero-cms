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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Controller class for services
 */
class ServicesControllerServices extends \Hubzero\Component\AdminController
{
	/**
	 * Services List
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		$this->view->filters = array();

		// Get paging variables
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		// Get sorting variables
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'category'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		// get all available services
		$objS = new Service($this->database);
		$this->view->rows = $objS->getServices('', 1, '', $this->view->filters['sort'], $this->view->filters['sort_Dir'], '', 1);

		$this->view->total = ($this->view->rows) ? count($this->view->rows) : 0;

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Initial setup of default jobs services
	 *
	 * @return     boolean Return description (if any) ...
	 */
	protected function setupServices()
	{
		$database = JFactory::getDBO();

		$objS = new Service($database);
		$now = JFactory::getDate()->toSql();

		$default1 = array(
			'id' => 0,
			'title' => JText::_('COM_SERVICES_BASIC_SERVICE_TITLE'),
			'category' => strtolower(JText::_('COM_SERVICES_JOBS')),
			'alias' => 'employer_basic',
			'status' => 1,
			'description' => JText::_('COM_SERVICES_BASIC_SERVICE_DESC'),
			'unitprice' => '0.00',
			'pointprice' => 0,
			'currency' => '$',
			'maxunits' => 6,
			'minunits' => 1,
			'unitsize' => 1,
			'unitmeasure' => strtolower(JText::_('month')),
			'changed' => $now,
			'params' => "promo=" . JText::_('COM_SERVICES_BASIC_SERVICE_PROMO') . "\npromomaxunits=3\nmaxads=1"
		);
		$default2 = array(
			'id' => 0,
			'title' => JText::_('COM_SERVICES_PREMIUM_SERVICE_TITLE'),
			'category' => strtolower(JText::_('COM_SERVICES_JOBS')),
			'alias' => 'employer_premium',
			'status' => 0,
			'description' => JText::_('COM_SERVICES_PREMIUM_SERVICE_DESC'),
			'unitprice' => '500.00',
			'pointprice' => 0,
			'currency' => '$',
			'maxunits' => 6,
			'minunits' => 1,
			'unitsize' => 1,
			'unitmeasure' => strtolower(JText::_('month')),
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
	 * Create a new subscription
	 * Displays the edit form
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit an entry
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			$id = JRequest::getVar('id', array(0));
			if (is_array($id))
			{
				$id = (!empty($id) ? intval($id[0]) : 0);
			}

			// load infor from database
			$this->view->row = new Service($this->database);
			$this->view->row->load($id);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->setLayout('edit')->display();
	}

	/**
	 * Save an entry and show the edit form
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Saves an entry and redirects to listing
	 *
	 * @return	void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new Service($this->database);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($redirect)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SERVICES_SAVED'),
				'message'
			);
		}

		$this->editTask($row);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}

