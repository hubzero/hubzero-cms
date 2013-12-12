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

ximport('Hubzero_Controller');
require_once(JPATH_COMPONENT . DS . 'tables' . DS . 'license.php');

/**
 * Manage publication categories (former resource types)
 */
class PublicationsControllerCategories extends Hubzero_Controller
{
	/**
	 * List types
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
			$this->_option . '.categories.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.categories.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['search']     = trim($app->getUserStateFromRequest(
			$this->_option . '.categories.search',
			'search',
			''
		));
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.categories.sort',
			'filter_order',
			'id'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.categories.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		
		$this->view->filters['state'] = 'all';
		
		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'publications.css');
		
		// Instantiate an object
		$rt = new PublicationCategory($this->database);

		// Get a record count
		$this->view->total = $rt->getCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getCategories($this->view->filters);

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
			$this->view->row = new PublicationCategory($this->database);
			$this->view->row->load($id);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}
		
		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'assets' . DS . 'css' . DS . 'publications.css');
		
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
		$row = new PublicationCategory($this->database);
		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}
		
		$row->contributable = JRequest::getInt('contributable', 0, 'post');
		$row->state 		= JRequest::getInt('state', 0, 'post');
				
		// Check content
		if (!$row->check())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id[]=' . $fields['id'],
				$row->getError(), 'error'
			);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$row->getError(), 'error'
			);
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Publication Category successfully saved')
		);
	}
	
	/**
	 * Change status 
	 * Redirects to list
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
		$row = new PublicationCategory($this->database);
		
		foreach ($ids as $id)
		{
			if (intval($id))
			{
				// Load row
				$row->load( $id );
				$row->state = $row->state == 1 ? 0 : 1;
				
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
			JText::_('Item(s) successfully published/unpublished')
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
