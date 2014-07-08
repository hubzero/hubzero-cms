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
 * Manage publication licenses
 */
class PublicationsControllerLicenses extends \Hubzero\Component\AdminController
{
	/**
	 * List resource types
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.licenses.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.licenses.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']     = trim($app->getUserStateFromRequest(
			$this->_option . '.licenses.search',
			'search',
			''
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.licenses.sort',
			'filter_order',
			'title'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.licenses.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS
			. 'assets' . DS . 'css' . DS . 'publications.css');

		// Instantiate an object
		$rt = new PublicationLicense($this->database);

		// Get a record count
		$this->view->total = $rt->getCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getRecords($this->view->filters);

		// initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total,
			$this->view->filters['start'],
			$this->view->filters['limit']
		);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Add a new type
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Edit a type
	 *
	 * @return     void
	 */
	public function editTask($row=null)
	{
		if ($row)
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming (expecting an array)
			$id = JRequest::getVar('id', array(0));
			if (is_array($id))
			{
				$id = $id[0];
			}
			else
			{
				$id = 0;
			}

			// Load the object
			$this->view->row = new PublicationLicense($this->database);
			$this->view->row->loadLicense($id);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option
			. DS . 'assets' . DS . 'css' . DS . 'publications.css');

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a type
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Initiate extended database class
		$row = new PublicationLicense($this->database);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		$row->customizable 	= JRequest::getInt('customizable', 0, 'post');
		$row->agreement 	= JRequest::getInt('agreement', 0, 'post');
		$row->apps_only 	= JRequest::getInt('apps_only', 0, 'post');
		$row->active 		= JRequest::getInt('active', 0, 'post');
		$row->icon			= $row->icon ? $row->icon : '/components/com_publications/images/logos/license.gif';

		if (!$row->id)
		{
			$row->ordering = $row->getNextOrder();
		}

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_PUBLICATIONS_SUCCESS_LICENSE_SAVED')
		);
	}

	public function orderupTask()
	{
		$this->reorderTask(-1);
	}

	public function orderdownTask()
	{
		$this->reorderTask(1);
	}

	/**
	 * Reorders licenses
	 * Redirects to license listing
	 *
	 * @return     void
	 */
	public function reorderTask($dir = 0)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array(0), '', 'array');

		// Load row
		$row = new PublicationLicense($this->database);
		$row->loadLicense( (int) $id[0]);

		// Update order
		$row->changeOrder($dir);

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Makes one license default
	 * Redirects to license listing
	 *
	 * @return     void
	 */
	public function makedefaultTask($dir = 0)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array(0), '', 'array');

		if (count($id) > 1)
		{
			$this->addComponentMessage(JText::_('COM_PUBLICATIONS_LICENSE_SELECT_ONE'), 'error');
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		// Initialize
		$row = new PublicationLicense($this->database);

		$id = intval($id[0]);

		// Load row
		$row->loadLicense( $id );

		// Make default
		$row->main = 1;

		// Save
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		// Fix up all other licenses
		$row->undefault($id);

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_PUBLICATIONS_SUCCESS_LICENSE_MADE_DEFAULT')
		);
	}

	/**
	 * Change license status
	 * Redirects to license listing
	 *
	 * @return     void
	 */
	public function changestatusTask($dir = 0)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array(0), '', 'array');

		// Initialize
		$row = new PublicationLicense($this->database);

		foreach ($ids as $id)
		{
			if (intval($id))
			{
				// Load row
				$row->loadLicense( $id );
				$row->active = $row->active == 1 ? 0 : 1;

				// Save
				if (!$row->store())
				{
					$this->addComponentMessage($row->getError(), 'error');
					$this->setRedirect(
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller
					);
					return;
				}
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('COM_PUBLICATIONS_SUCCESS_LICENSE_PUBLISHED')
		);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect('index.php?option=' . $this->_option . '&controller=' . $this->_controller);
	}
}
