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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for featured items
 */
class FeaturesControllerItems extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();

		parent::execute();
	}

	/**
	 * Display a list of featured items
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit'] = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'), 'request');
		$this->view->filters['start'] = JRequest::getInt('limitstart', 0, 'get');
		$this->view->filters['type']  = JRequest::getVar('type', '');

		// Instantiate a FeaturesHistory object
		$obj = new FeaturesHistory($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters, $this->config->get('access-manage-component'));

		// Get records
		$this->view->rows = $obj->getRecords($this->view->filters, $this->config->get('access-manage-component'));

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Push some styles to the template
		$this->_getStyles($this->_option, 'assets/css/features.css');

		// Set the page title
		$this->view->title = JText::_(strtoupper($this->_option));
		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}

		$this->view->config = $this->config;

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Add a feature
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a feature
	 * 
	 * @return     void
	 */
	public function editTask($row=null)
	{
		// Check if they are authorized to make changes
		if (!$this->config->get('access-manage-component')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
			);
			return;
		}

		$this->view->setLayout('edit');

		// Incoming
		$id = JRequest::getInt('id', 0, 'request');

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			// Load the object
			$this->view->row = new FeaturesHistory($this->database);
			$this->view->row->load($id);
		}

		if ($this->view->row->note == 'tools') 
		{
			$this->view->row->tbl = 'tools';
		} 
		else if ($this->view->row->note == 'nontools') 
		{
			$this->view->row->tbl = 'resources';
		}

		if (!$this->view->row->featured) 
		{
			$this->view->row->featured = date("Y") . '-' . date("m") . '-'.date("d") . ' 00:00:00';
		}

		// Set the page title
		$this->view->title = JText::_(strtoupper($this->_option)) . ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		$document =& JFactory::getDocument();
		$document->setTitle($this->view->title);

		// Set the pathway
		$pathway =& JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0) 
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
			);
		}

		// Push some styles to the template
		$this->_getStyles($this->_option, 'assets/css/features.css');

		$this->view->config = $this->config;

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Save an entry
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check if they are authorized to make changes
		if (!$this->config->get('access-manage-component')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
			);
			return;
		}

		// Instantiate an object and bind the incoming data
		$row = new FeaturesHistory($this->database);
		if (!$row->bind($fields)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($row->tbl == 'tools') 
		{
			$row->note = 'tools';
			$row->tbl = 'resources';
		} 
		else if ($row->tbl == 'resources') 
		{
			$row->note = 'nontools';
			$row->tbl = 'resources';
		}

		// Check content
		if (!$row->check()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
		);
	}

	/**
	 * Remove an entry
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		// Check if they are authorized to make changes
		if (!$this->config->get('access-manage-component')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
			);
			return;
		}

		// Incoming
		$id = JRequest::getInt('id', 0, 'request');

		if ($id) 
		{
			// Delete the object
			$row = new FeaturesHistory($this->database);
			$row->delete($id);
		}

		// Redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller)
		);
	}

	/**
	 * Authorization checks
	 * 
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if ($this->juser->get('guest')) 
		{
			return;
		}

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
		}
		else 
		{
			if ($this->juser->authorize($this->_option, 'manage'))
			{
				$this->config->set('access-manage-' . $assetType, true);
				$this->config->set('access-admin-' . $assetType, true);
				$this->config->set('access-create-' . $assetType, true);
				$this->config->set('access-delete-' . $assetType, true);
				$this->config->set('access-edit-' . $assetType, true);
			}
		}
	}
}

