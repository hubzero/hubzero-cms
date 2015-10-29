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

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'middleware.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.zones.php');

/**
 * Tools controller class for tool versions
 */
class ToolsControllerVersions extends \Hubzero\Component\AdminController
{
	/**
	 * Display all versions for a specific entry
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Push some styles to the template
		$document = JFactory::getDocument();
		$document->addStyleSheet('components' . DS . $this->_option . DS . 'tools.css');

		$app = JFactory::getApplication();
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

		if (!$this->view->filters['id'])
		{
			JError::raiseError(404, JText::_('COM_TOOLS_ERROR_MISSING_ID'));
		}
		$this->view->tool = ToolsModelTool::getInstance($this->view->filters['id']);
		if (!$this->view->tool)
		{
			JError::raiseError(404, JText::_('COM_TOOLS_ERROR_TOOL_NOT_FOUND'));
		}
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

		$this->view->parent = ToolsModelTool::getInstance($id);

		$this->view->row = ToolsModelVersion::getInstance($version);

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
				JText::_('COM_TOOLS_ERROR_MISSING_ID'),
				'error'
			);
			return;
		}

		$row = ToolsModelVersion::getInstance(intval($fields['version']));
		if (!$row)
		{
			JRequest::setVar('id', $fields['id']);
			JRequest::setVar('version', $fields['version']);
			$this->addComponentMessage(JText::_('COM_TOOLS_ERROR_TOOL_NOT_FOUND'), 'error');
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
			$this->addComponentMessage(JText::_('COM_TOOLS_ERROR_MISSING_COMMAND'), 'error');
			$this->editTask($row);
			return;
		}

		$row->hostreq = (is_array($row->hostreq) ? explode(',', $row->hostreq[0]) : explode(',', $row->hostreq));

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
				JText::_('COM_TOOLS_ITEM_SAVED')
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

	/**
	 * Display a list of version zones
	 *
	 * @return     void
	 */
	public function displayZonesTask()
	{
		$this->view->setLayout('component');

		// Get the version number
		$version = JRequest::getInt('version', 0);

		// Do we have an ID?
		if (!$version)
		{
			$this->cancelTask();
			return;
		}

		// Get the table
		$db = \JFactory::getDbo();
		$this->view->rows    = with(new ToolVersionZones($db))->loadByToolVersion($version);
		$this->view->version = $version;

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
	 * Add a new zone
	 *
	 * @return     void
	 */
	public function addZoneTask()
	{
		$this->editZoneTask();
	}

	/**
	 * Edit a zone
	 *
	 * @return     void
	 */
	public function editZoneTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('editZone');

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else
		{
			// Incoming
			$id = JRequest::getInt('id', 0);
			$db = \JFactory::getDbo();
			$this->view->row = new ToolVersionZones($db);
			$this->view->row->load($id);
		}

		if (!$this->view->row->get('tool_version_id'))
		{
			$this->view->row->set('tool_version_id', \JRequest::getInt('version', 0));
		}

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
	 * Save changes to version zone
	 *
	 * @return     void
	 */
	public function saveZoneTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		$db  = \JFactory::getDbo();
		$row = new ToolVersionZones($db);

		if (empty($fields['publish_up'])) $fields['publish_up'] = null;
		if (empty($fields['publish_down'])) $fields['publish_down'] = null;

		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store new content
		if (!$row->store(true))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		JRequest::setVar('tmpl', 'component');

		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
		else
		{
			echo '<p class="message">' . JText::_('COM_TOOLS_ITEM_SAVED') . '</p>';
		}
	}

	/**
	 * Delete zone entry
	 *
	 * @return     void
	 */
	public function removeZoneTask()
	{
		// Check for request forgeries
		JRequest::checkToken(array('get', 'post')) or jexit('Invalid Token');

		// Incoming
		if ($id = JRequest::getInt('id', false))
		{
			$db  = \JFactory::getDbo();
			$row = new ToolVersionZones($db);
			$row->load($id);

			if (!$row->delete())
			{
				JError::raiseError(500, $row->getError());
				return;
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&tmpl=component&task=displayZones&version=' . JRequest::getInt('version', 0),
			JText::_('COM_TOOLS_ITEM_DELETED'),
			'message'
		);
	}
}