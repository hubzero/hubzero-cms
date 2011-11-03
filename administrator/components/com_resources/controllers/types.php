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
 * Manage resource types
 */
class ResourcesControllerTypes extends Hubzero_Controller
{
	/**
	 * List resource types
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.types.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.types.limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.types.sort', 
			'filter_order', 
			'category'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.types.sortdir', 
			'filter_order_Dir', 
			'DESC'
		));
		$this->view->filters['category'] = $app->getUserStateFromRequest(
			$this->_option . '.types.category', 
			'category', 
			0, 
			'int'
		);

		// Instantiate an object
		$rt = new ResourcesType($this->database);

		// Get a record count
		$this->view->total = $rt->getAllCount($this->view->filters);

		// Get records
		$this->view->rows = $rt->getAllTypes($this->view->filters);

		// initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Get the category names
		$this->view->cats = $rt->getTypes('0');

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
			$this->view->row = new ResourcesType($this->database);
			$this->view->row->load($id);
		}

		// Get the categories
		$this->view->categories = $this->view->row->getTypes(0);

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

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

		// Initiate extended database class
		$row = new ResourcesType($this->database);
		if (!$row->bind($_POST)) 
		{
			echo ResourcesHtml::alert($row->getError());
			exit();
		}
		$row->contributable = ($row->contributable) ? $row->contributable : '0';
		$row->alias = ($row->alias) ? $row->alias : $this->_normalize($row->type, true);
		
		// Get the custom fields
		$fields = JRequest::getVar('fields', array(), 'post');
		if (is_array($fields)) 
		{
			$txta = array();
			foreach ($fields as $val)
			{
				if ($val['title']) 
				{
					$k = $this->_normalize(trim($val['title']));
					$t = str_replace('=', '-', $val['title']);
					$j = (isset($val['type']))     ? $val['type']     : 'text';
					$x = (isset($val['required'])) ? $val['required'] : '0';
					$txta[] = $k . '=' . $t . '=' . $j . '=' . $x;
				}
			}
			$field = implode("\n", $txta);
			$row->customFields = $field;
		}

		// Get parameters
		$params = JRequest::getVar('params', '', 'post');
		if (is_array($params)) 
		{
			$txt = array();
			foreach ($params as $k => $v)
			{
				$txt[] = "$k=$v";
			}
			$row->params = implode("\n", $txt);
		}

		// Check content
		if (!$row->check()) 
		{
			$this->_message = $row->getError();
			$this->_messageType = 'error';
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->_message = $row->getError();
			$this->_messageType = 'error';
			$this->editTask($row);
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Type successfully saved')
		);
	}

	/**
	 * Strip any non-alphanumeric characters and make lowercase
	 * 
	 * @param      string  $txt    String to normalize
	 * @param      boolean $dashes Allow dashes and underscores
	 * @return     string
	 */
	private function _normalize($txt, $dashes=false)
	{
		$allowed = "a-zA-Z0-9";
		if ($dashes)
		{
			$allowed = "a-zA-Z0-9\-_";
		}
		return strtolower(preg_replace("/[^$allowed]/", '', $txt));
	}

	/**
	 * Remove one or more types
	 * 
	 * @return     void Redirects back to main listing
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming (expecting an array)
		$ids = JRequest::getVar('id', array());

		// Ensure we have an ID to work with
		if (empty($ids)) 
		{
			// Redirect with error message
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('No type selected'),
				'error'
			);
			return;
		}

		$rt = new ResourcesType($this->database);

		foreach ($ids as $id)
		{
			// Check if the type is being used
			$total = $rt->checkUsage($id);

			if ($total > 0) 
			{
				// Redirect with error message
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::sprintf('There are resources with type %s. Please reassign them before deleting this type.', $id),
					'error'
				);
				return;
			}

			// Delete the type
			$rt->delete($id);
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Type successfully saved')
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
