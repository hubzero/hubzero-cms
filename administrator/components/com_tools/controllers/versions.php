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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Tool');
ximport('Hubzero_Tool_Version');
ximport('Hubzero_Controller');

/**
 * Short description for 'ContribtoolController'
 * 
 * Long description (if any) ...
 */
class ToolsControllerVersions extends Hubzero_Controller
{
	/**
	 * Display all versions for a specific entry
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Push some styles to the template
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'tools.css');

		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get Filters
		$this->view->filters = array();
		$this->view->filters['id']          = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.versions.id', 
			'id', 
			0,
			'int'
		);

		// Get filters
		/*$this->view->filters['search']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'search', 
			'search', 
			''
		));
		$this->view->filters['search_field'] = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'search_field', 
			'search_field', 
			'all'
		));*/
		
		// Sorting
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'sort', 
			'filter_order', 
			'toolname'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'sortdir', 
			'filter_order_Dir', 
			'ASC'
		));
		$this->view->filters['sortby'] = $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'];

		// Get paging variables
		$this->view->filters['limit']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.versions.' . $this->view->filters['id'] . 'limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);
		
		$this->view->tool = Hubzero_Tool::getInstance($this->view->filters['id']);
		$this->view->total = count($this->view->tool->version);
		$this->view->rows = $this->view->tool->getToolVersionSummaries($this->view->filters, true);
		
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

		// Display results
		$this->view->display();
	}

	/**
	 * Edit an entry version
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function editTask()
	{
		JRequest::setVar('hidemainmenu', 1);
		
		// Incoming instance ID
		$id = JRequest::getInt('id', 0);
		$version = JRequest::getInt('version', 0);

		// Do we have an ID?
		if (!$id || !$version) 
		{
			$this->cancelTask();
			return;
		}

		$this->view->parent = Hubzero_Tool::getInstance($id);

		$this->view->row = Hubzero_Tool_Version::getInstance($version);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Display results
		$this->view->display();
	}

	/**
	 * Save an entry version and show the edit form
	 * 
	 * @return     void
	 */
	public function applyTask()
	{
	    $this->saveTask();
	}

	/**
	 * Save an entry version
	 * 
	 * @param      boolean $redirect Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function saveTask($redirect = true)
	{
		// Incoming instance ID
		$fields = JRequest::getVar('fields', array(), 'post');

		// Do we have an ID?
		if (!$fields['version']) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Missing ID'),
				'error'
			);
			return;
		}
		
		$row = Hubzero_Tool_Version::getInstance(intval($fields['version']));
		if (!$row)
		{
			JRequest::setVar('id', $fields['id']);
			JRequest::setVar('version', $fields['version']);
			$this->addComponentMessage(JText::_('Tool instance not found'), 'error');
			$this->editTask();
			return;
		}

		$row->vnc_command = trim($fields['vnc_command']);
		$row->vnc_timeout = ($fields['vnc_timeout'] != 0) ? intval(trim($fields['vnc_timeout'])) : NULL;
		$row->hostreq     = trim($fields['hostreq']);
		$row->mw          = trim($fields['mw']);
		$row->params      = trim($fields['params']);

		if (!$row->vnc_command)
		{
			$this->addComponentMessage(JText::_('No command value'), 'error');
			$this->editTask($row);
			return;
		}

		$row->hostreq = explode(',', $row->hostreq);

		$hostreq = array();
		foreach ($row->hostreq as $req)
		{
			if (!empty($req))
			{
				$hostreq[] = trim($req);
			}
		}

		$row->hostreq = $hostreq;
		$row->update();

		if ($this->_task == 'apply') 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&id=' . $fields['id'] . '&version=' . $fields['version']
			);
			return;
		}
		else 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('TOOL_VERSION_SAVED')
			);
		}
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
