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
class CronControllerPlugins extends Hubzero_Controller
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$task = JRequest::getVar('task', '');
		$plugin = JRequest::getVar('plugin', '');
		if ($plugin && $task && $task != 'manage') //!isset($this->_taskMap[$task]))
		{
			JRequest::setVar('action', $task);
			JRequest::setVar('task', 'manage');
		}

		$this->_folder = 'cron';

		parent::execute();
	}

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
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'ordering'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));
		$this->view->filters['state']    = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.state',
			'state',
			'',
			'word'
		);
		$this->view->filters['search']    = urldecode($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.search',
			'search',
			'',
			'word'
		));

		$where = array();
		$this->client = JRequest::getWord('filter_client', 'site');

		if ($this->client == 'admin') 
		{
			$where[] = 'p.client_id = 1';
			$client_id = 1;
		} 
		else 
		{
			$where[] = 'p.client_id = 0';
			$where[] = 'p.folder = ' . $this->database->Quote($this->_folder);
			$client_id = 0;
		}

		if ($this->view->filters['search']) 
		{
			$where[] = 'LOWER(p.name) LIKE ' . $this->database->Quote('%' . $this->database->getEscaped($this->view->filters['search'], true) . '%', false);
		}
		if ($this->view->filters['state']) 
		{
			if ($this->view->filters['state'] == 'P') 
			{
				$where[] = 'p.published = 1';
			} 
			else if ($this->view->filters['state'] == 'U') 
			{
				$where[] = 'p.published = 0';
			}
		}
		if (version_compare(JVERSION, '1.6', 'ge')) 
		{
			$where[] = 'p.type = ' . $this->database->Quote('plugin');
		}

		$where   = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$orderby = ' ORDER BY ' . $this->view->filters['sort'] . ' ' . $this->view->filters['sort_Dir'] . ', p.ordering ASC';

		// get the total number of records
		if (version_compare(JVERSION, '1.6', 'lt')) 
		{
			$query = 'SELECT COUNT(*)'
				. ' FROM #__plugins AS p'
				. $where;
		}
		else 
		{
			$query = 'SELECT COUNT(*)'
				. ' FROM #__extensions AS p'
				. $where;
		}
		$this->database->setQuery($query);
		$this->view->total = $this->database->loadResult();

		jimport('joomla.html.pagination');
		$this->view->pagination = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		if (version_compare(JVERSION, '1.6', 'lt')) 
		{
			$query = 'SELECT p.*, u.name AS editor, g.name AS groupname'
				. ' FROM #__plugins AS p'
				. ' LEFT JOIN #__users AS u ON u.id = p.checked_out'
				. ' LEFT JOIN #__groups AS g ON g.id = p.access'
				. $where
				. ' GROUP BY p.id'
				. $orderby;
		}
		else 
		{
			$query = 'SELECT p.extension_id AS id, p.enabled As published, p.*, u.name AS editor, g.title AS groupname'
				. ' FROM #__extensions AS p'
				. ' LEFT JOIN #__users AS u ON u.id = p.checked_out'
				. ' LEFT JOIN #__viewlevels AS g ON g.id = p.access'
				. $where
				. ' GROUP BY p.extension_id'
				. $orderby;
		}
		$this->database->setQuery($query, $this->view->pagination->limitstart, $this->view->pagination->limit);
		$this->view->rows = $this->database->loadObjectList();
		if ($this->database->getErrorNum()) 
		{
			JError::raiseError(500, $this->database->stderr());
			return false;
		}
		
		// Get related plugins
		JPluginHelper::importPlugin($this->_folder);
		$dispatcher =& JDispatcher::getInstance();
		
		// Show related content
		$this->view->manage = $dispatcher->trigger('onCanManage');

		$this->view->client = $this->client;
		$this->view->states = JHTML::_('grid.state', $this->view->filters['state']);
		$this->view->user = $this->juser;

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit a type
	 * 
	 * @return     void
	 */
	public function manageTask()
	{
		// Incoming (expecting an array)
		$plugin = JRequest::getVar('plugin', '');

		if (!$plugin)
		{
			// Redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Please select a plugin to manage.')
			);
		}

		// Get related plugins
		JPluginHelper::importPlugin($this->_folder, $plugin);
		$dispatcher =& JDispatcher::getInstance();
		
		// Show related content
		$out = $dispatcher->trigger(
			'onManage', 
			array(
				$this->_option, 
				$this->_controller,
				JRequest::getVar('action', 'default')
			)
		);

		$this->view->html = '';

		if (count($out) > 0) 
		{
			foreach ($out as $o) 
			{
				$this->view->html .= $o;
			}
		}

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
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Create a new plugin
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}
	
	/**
	 * Edit a plugin
	 * 
	 * @param      object $row JPluginTable
	 * @return     void
	 */
	public function editTask($row = null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		$client = JRequest::getWord('client', 'site');

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			$cid = JRequest::getVar('cid', array(0), '', 'array');
			JArrayHelper::toInteger($cid, array(0));
			
			$this->view->row = JTable::getInstance('plugin');

			// load the row from the db table
			$this->view->row->load($cid[0]);
		}
		
		// Is this entry checked out?
		if ($this->view->row->isCheckedOut($this->juser->get('id')))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client, 
				JText::sprintf('DESCBEINGEDITTED', JText::_('The plugin'), $this->view->row->title), 
				'error'
			);
			return;
		}

		$this->view->lists = array();

		// get list of groups
		if ($this->view->row->access == 99 || $this->view->row->client_id == 1) 
		{
			$this->view->lists['access'] = 'Administrator<input type="hidden" name="access" value="99" />';
		} 
		else 
		{
			// build the html select list for the group access
			$this->view->lists['access'] = JHTML::_('list.accesslevel', $this->view->row);
		}

		if ($cid[0])
		{
			$this->view->row->checkout($this->juser->get('id'));

			$lang =& JFactory::getLanguage();
			$lang->load('plg_' . trim($this->view->row->folder) . '_' . trim($this->view->row->element), JPATH_ADMINISTRATOR);

			$data = JApplicationHelper::parseXMLInstallFile(JPATH_SITE . DS . 'plugins' . DS  .$this->view->row->folder . DS . $this->view->row->element . '.xml');

			$this->view->row->description = $data['description'];
		} 
		else 
		{
			$this->view->row->folder 		= $this->_folder;
			$this->view->row->ordering 		= 999;
			$this->view->row->published 	= 1;
			$this->view->row->description 	= '';
		}

		if ($this->view->row->ordering > -10000 && $this->view->row->ordering < 10000)
		{
			// build the html select list for ordering
			$query = 'SELECT ordering AS value, name AS text'
				. ' FROM #__plugins'
				. ' WHERE folder = '.$this->database->Quote($this->_folder)
				. ' AND published > 0'
				. ' AND '. ($client == 'admin' ? "client_id='1'" : "client_id='0'")
				. ' AND ordering > -10000'
				. ' AND ordering < 10000'
				. ' ORDER BY ordering'
			;
			$order = JHTML::_('list.genericordering',  $query);
			
			$this->view->lists['ordering'] = JHTML::_('select.genericlist', $order, 'ordering', 'class="inputbox" size="1"', 'value', 'text', intval($this->view->row->ordering));
		} 
		else 
		{
			$this->view->lists['ordering'] = '<input type="hidden" name="ordering" value="' . $this->view->row->ordering . '" />' . JText::_('This plugin cannot be reordered');
		}

		$this->view->lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $this->view->row->published);

		$paramsClass = 'JParameter';
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$paramsClass = 'JRegistry';
		}

		$this->view->params = new $paramsClass(
			$this->view->row->params, 
			JApplicationHelper::getPath('plg_xml', $this->view->row->folder . DS . $this->view->row->element), 
			'plugin'
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
	 * Save changes to a plugin
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$client = JRequest::getWord('filter_client', 'site');

		// Bind data
		$row =& JTable::getInstance('plugin');
		if (!$row->bind(JRequest::get('post'))) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		// Check content
		if (!$row->check()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->store()) 
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($row);
			return;
		}
		
		$row->checkin();
		$row->reorder(
			'folder = ' . $this->database->Quote($row->folder) . ' 
			AND ordering > -10000 
			AND ordering < 10000 
			AND (' . ($client == 'admin' ? "client_id=1" : "client_id=0") . ')'
		);

		switch ($this->_task)
		{
			case 'apply':
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client . '&task=edit&cid[]=' . $row->id, 
					JText::sprintf('Successfully Saved changes to Plugin', $row->name)
				);
			break;

			case 'save':
			default:
				$msg = JText::sprintf('Successfully Saved Plugin', $row->name);
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client, 
					$msg
				);
			break;
		}
	}

	/**
	 * Calls stateTask to publish entries
	 * 
	 * @return     void
	 */
	public function publishTask()
	{
		$this->stateTask(1);
	}

	/**
	 * Calls stateTask to unpublish entries
	 * 
	 * @return     void
	 */
	public function unpublishTask()
	{
		$this->stateTask(0);
	}

	/**
	 * Set the state of a plugin
	 * 
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function stateTask($state=0)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit('Invalid Token');

		// Incoming
		$id = JRequest::getVar('id', array(0), '', 'array');
		JArrayHelper::toInteger($id, array(0));

		$client = JRequest::getWord('filter_client', 'site');

		if (count($id) < 1) 
		{
			$action = $state ? JText::_('publish') : JText::_('unpublish');
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client,
				JText::_('Select a plugin to ' . $action),
				'error'
			);
			return;
		}

		$query = 'UPDATE #__plugins SET published = '.(int) $state
			. ' WHERE id IN (' . implode(',', $id) . ')'
			. ' AND (checked_out = 0 OR (checked_out = '.(int) $this->juser->get('id').'))';

		$this->database->setQuery($query);
		if (!$this->database->query()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client,
				$this->database->getErrorMsg(),
				'error'
			);
			return;
		}

		if (count($id) == 1) 
		{
			$row =& JTable::getInstance('plugin');
			$row->checkin($id[0]);
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&client=' . $client
		);
	}
	
	/**
	 * Reorder a plugin
	 * 
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function orderTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$cid 	= JRequest::getVar('cid', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid, array(0));

		$uid    = $cid[0];
		$inc    = ($this->_task == 'orderup' ? -1 : 1);
		$client = JRequest::getWord('filter_client', 'site');

		// Currently Unsupported
		if ($client == 'admin') 
		{
			$where = "client_id = 1";
		} 
		else 
		{
			$where = "client_id = 0";
		}
		
		$row =& JTable::getInstance('plugin');
		$row->load($uid);
		$row->move($inc, 'folder='.$this->database->Quote($row->folder).' AND ordering > -10000 AND ordering < 10000 AND ('.$where.')');

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Set the state of an article to 'public'
	 * 
	 * @return     void
	 */
	public function accesspublicTask()
	{
		return $this->accessTask(0);
	}

	/**
	 * Set the state of an article to 'registered'
	 * 
	 * @return     void
	 */
	public function accessregisteredTask()
	{
		return $this->accessTask(1);
	}

	/**
	 * Set the state of an article to 'special'
	 * 
	 * @return     void
	 */
	public function accessspecialTask()
	{
		return $this->accessTask(2);
	}

	/**
	 * Set the access of a plugin
	 * 
	 * @param      integer $access Access level to set
	 * @return     void
	 */
	public function accessTask($access=0)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$cid = JRequest::getVar('id', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid, array(0));

		// Load the object
		$row =& JTable::getInstance('plugin');
		$row->load($cid[0]);

		// Set the access
		$row->access = $access;

		// Check data
		if (!$row->check()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$row->getError(),
				'error'
			);
			return;
		}

		// Store data
		if (!$row->store()) 
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$row->getError(),
				'error'
			);
			return;
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Save the ordering for an array of plugins
	 * 
	 * @return     void
	 */
	public function saveorderTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$cid = JRequest::getVar('cid', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid, array(0));

		$total = count($cid);
		$order = JRequest::getVar('order', array(0), 'post', 'array');
		JArrayHelper::toInteger($order, array(0));

		$row =& JTable::getInstance('plugin');
		$conditions = array();

		// update ordering values
		for ($i=0; $i < $total; $i++)
		{
			$row->load((int) $cid[$i]);
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) 
				{
					$this->setRedirect(
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
						$this->database->getErrorMsg(),
						'error'
					);
					return;
				}
				// remember to updateOrder this group
				$condition = 'folder = ' . $this->database->Quote($row->folder) . ' AND ordering > -10000 AND ordering < 10000 AND client_id = ' . (int) $row->client_id;
				$found = false;
				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition) 
					{
						$found = true;
						break;
					}
				}
				if (!$found) $conditions[] = array($row->id, $condition);
			}
		}

		// execute updateOrder for each group
		foreach ($conditions as $cond) 
		{
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}

		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('New ordering saved')
		);
	}
}
