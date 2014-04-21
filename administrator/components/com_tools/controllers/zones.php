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

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'mw.zones.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'zone.locations.php');

/**
 * Administrative tools controller for zones
 */
class ToolsControllerZones extends Hubzero_Controller
{
	/**
	 * Display a list of hosts
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		// Get filters
		$this->view->filters = array();
		$this->view->filters['zone']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.zone', 
			'zone', 
			''
		));
		$this->view->filters['master']       = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.master', 
			'master', 
			''
		));
		// Sorting
		$this->view->filters['sort']         = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'filter_order', 
			'zone'
		));
		$this->view->filters['sort_Dir']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'ASC'
		));
		// Get paging variables
		$this->view->filters['limit']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		// In case limit has been changed, adjust limitstart accordingly
		$this->view->filters['start'] = ($this->view->filters['limit'] != 0 ? (floor($this->view->filters['start'] / $this->view->filters['limit']) * $this->view->filters['limit']) : 0);

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		$model = new MwZones($mwdb);

		$this->view->total = $model->getCount($this->view->filters);

		$this->view->rows = $model->getRecords($this->view->filters);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		$componentcss = JPATH_COMPONENT . DS . 'tools.css';
		if (file_exists($componentcss)) 
		{
			$jdocument = JFactory::getDocument();
			$jdocument->addStyleSheet('components' . DS . $this->_option . DS . 'tools.css?v=' . filemtime($componentcss));
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
	 * Edit a record
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a record
	 * 
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			// Incoming
			$id = JRequest::getInt('id', 0);

			$this->view->row = new MwZones($mwdb);
			$this->view->row->load($id);
		}
		if (!$this->view->row->id)
		{
			$this->view->row->state = 'down';
		}

		$vl = new MwZoneLocations($mwdb);

		$this->view->locations = $vl->getRecords(array('zone_id' => $this->view->row->id));

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
         * Edit a location
         *
         * @return     void
         */
        public function locationsTask($row=null)
        {
                JRequest::setVar('hidemainmenu', 1);

                $this->view->setLayout('locations');

                // Get the middleware database
                $mwdb = MwUtils::getMWDBO();

                if (is_object($row))
                {
                        $this->view->row = $row;
                }
                else
                {
                        // Incoming
                        $id = JRequest::getInt('id', 0);

                        $this->view->row = new MwZones($mwdb);
                        $this->view->row->load($id);
                }
                if (!$this->view->row->id)
                {
                        $this->view->row->state = 'down';
                }

                $vl = new MwZoneLocations($mwdb);

                $this->view->locations = $vl->getRecords(array('zone_id' => $this->view->row->id));

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
	 * Save changes to a record
	 * 
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save changes to a record
	 * 
	 * @param      boolean $redirect Redirect after save?
	 * @return     void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		// Incoming
		$fields = JRequest::getVar('fields', array(), 'post');

		$row = new MwZones($mwdb);
		if (!$row->bind($fields)) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
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

		$vl = new MwZoneLocations($mwdb);
		$vl->deleteByZone($row->id);

		$locations = JRequest::getVar('locations', array(), 'post');
		foreach ($locations as $location)
		{
			$vl = new MwZoneLocations($mwdb);
			$vl->zone_id = $row->id;
			$vl->location = $location;
			if (!$vl->check())
			{
				$this->addComponentMessage($vl->getError(), 'error');
				$this->editTask($row);
				return;
			}
			if (!$vl->store())
			{
				$this->addComponentMessage($vl->getError(), 'error');
				$this->editTask($row);
				return;
			}
		}
		/*$customs = JRequest::getVar('custom', array(), 'post');
		foreach ($customs as $custom)
		{
			$vl = new MwZoneLocations($mwdb);
			$vl->zone_id = $row->id;
			$vl->location = $custom;
			if (!$vl->check())
			{
				$this->addComponentMessage($vl->getError(), 'error');
				$this->editTask($row);
				return;
			}
			if (!$vl->store())
			{
				$this->addComponentMessage($vl->getError(), 'error');
				$this->editTask($row);
				return;
			}
		}*/

		if ($redirect)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				Jtext::_('Zone successfully saved.'),
				'message'
			);
			return;
		}

		$this->editTask($row);
	}

	/**
	 * Toggle a zone's state
	 * 
	 * @return     void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getInt('id', 0);
		$state = strtolower(JRequest::getWord('state', 'up'));

		if ($state != 'up' && $state != 'down')
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}

		// Get the middleware database
		$mwdb = MwUtils::getMWDBO();

		$row = new MwZones($mwdb);
		if ($row->load($id))
		{
			$row->state = $state;
			if (!$row->store())
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('State update failed.'),
					'error'
				);
				return;
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Delete one or more records
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		$mwdb = MwUtils::getMWDBO();

		if (count($ids) > 0) 
		{
			$row = new MwZones($mwdb);

			// Loop through each ID
			foreach ($ids as $id) 
			{
				if (!$row->delete(intval($id))) 
				{
					JError::raiseError(500, $row->getError());
					return;
				}
			}
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Zone successfully deleted.'),
			'message'
		);
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
