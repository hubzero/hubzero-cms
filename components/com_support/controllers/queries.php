<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'query.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'queryfolder.php');

/**
 * Support controller class for ticket queries
 */
class SupportControllerQueries extends \Hubzero\Component\SiteController
{
	/**
	 * Displays a list of records
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=tickets&task=display'
		);
	}

	/**
	 * Create a new record
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Display a form for adding/editing a record
	 *
	 * @return	void
	 */
	public function editTask()
	{
		$this->view->setLayout('edit');

		$this->view->lists = array();

		// Get resolutions
		$sr = new SupportResolution($this->database);
		$this->view->lists['resolutions'] = $sr->getResolutions();

		$this->view->lists['severities'] = SupportUtilities::getSeverities($this->config->get('severities'));

		$id = JRequest::getInt('id', 0);

		$this->view->row = new SupportQuery($this->database);
		$this->view->row->load($id);
		if (!$this->view->row->sort)
		{
			$this->view->row->sort = 'created';
		}
		if (!$this->view->row->sort_dir)
		{
			$this->view->row->sort_dir = 'desc';
		}

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'conditions.php');
		$con = new SupportModelConditions();
		$this->view->conditions = $con->getConditions();

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getError() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new record
	 *
	 * @return	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields  = JRequest::getVar('fields', array(), 'post');
		$no_html = JRequest::getInt('no_html', 0);
		$tmpl    = JRequest::getVar('component', '');

		$row = new SupportQuery($this->database);
		if (!$row->bind($fields))
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->addComponentMessage($row->getError(), 'error');
				$this->editTask($row);
			}
			else
			{
				echo $row->getError();
			}
			return;
		}

		// Check content
		if (!$row->check())
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->addComponentMessage($row->getError(), 'error');
				$this->editTask($row);
			}
			else
			{
				echo $row->getError();
			}
			return;
		}

		// Store new content
		if (!$row->store())
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->addComponentMessage($row->getError(), 'error');
				$this->editTask($row);
			}
			else
			{
				echo $row->getError();
			}
			return;
		}

		if (!$no_html && $tmpl != 'component')
		{
			// Output messsage and redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=tickets&task=display&show=' . $row->id)
			);
		}
		else
		{
			$this->listTask();
		}
	}

	/**
	 * Delete one or more records
	 *
	 * @return	void
	 */
	public function removeTask()
	{
		// Incoming
		$id      = JRequest::getInt('id', 0);
		$no_html = JRequest::getInt('no_html', 0);
		$tmpl    = JRequest::getVar('component', '');

		// Check for an ID
		if (!$id)
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=tickets&task=display',
					JText::_('COM_SUPPORT_ERROR_SELECT_QUERY_TO_DELETE'),
					'error'
				);
			}
			return;
		}

		$row = new SupportQuery($this->database);
		// Delete message
		$row->delete(intval($id));

		if (!$no_html && $tmpl != 'component')
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=tickets&task=display'
			);
		}
		else
		{
			$this->listTask();
		}
	}

	/**
	 * Build the query list
	 *
	 * @return  void
	 */
	public function listTask()
	{
		$obj = new SupportTicket($this->database);

		// Get query list
		$sf = new SupportTableQueryFolder($this->database);
		$this->view->folders = $sf->find('list', array(
			'user_id'  => $this->juser->get('id'),
			'sort'     => 'ordering',
			'sort_Dir' => 'asc'
		));

		$sq = new SupportQuery($this->database);
		$queries = $sq->find('list', array(
			'user_id'  => $this->juser->get('id'),
			'sort'     => 'ordering',
			'sort_Dir' => 'asc'
		));

		foreach ($queries as $query)
		{
			$query->query = $sq->getQuery($query->conditions);
			$query->count = $obj->getCount($query->query);

			foreach ($this->view->folders as $k => $v)
			{
				if (!isset($this->view->folders[$k]->queries))
				{
					$this->view->folders[$k]->queries = array();
				}
				if ($query->folder_id == $v->id)
				{
					$this->view->folders[$k]->queries[] = $query;
				}
			}
		}

		$this->view->show = 0;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('list')
			->display();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=tickets&task=display'
		);
	}

	/**
	 * Create a new folder
	 *
	 * @return	void
	 */
	public function addfolderTask()
	{
		$this->editfolderTask();
	}

	/**
	 * Display a form for adding/editing a folder
	 *
	 * @return	void
	 */
	public function editfolderTask($row=null)
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

			$this->view->row = new SupportTableQueryFolder($this->database);
			$this->view->row->load($id);
		}

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getError() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->setLayout('editfolder')->display();
	}

	/**
	 * Save a folder
	 *
	 * @return  void
	 */
	public function applyfolderTask()
	{
		$this->savefolderTask(false);
	}

	/**
	 * Save a folder
	 *
	 * @return  void
	 */
	public function savefolderTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$fields  = JRequest::getVar('fields', array());
		$no_html = JRequest::getInt('no_html', 0);
		$tmpl    = JRequest::getVar('component', '');

		$response = new stdClass;
		$response->success = 1;
		$response->message = '';

		$row = new SupportTableQueryFolder($this->database);
		if (!$row->bind($fields))
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->addComponentMessage($row->getError(), 'error');
				$this->editfolderTask($row);
			}
			else
			{
				$response->success = 0;
				$response->message = $row->getError();
				echo json_encode($response);
			}
			return;
		}

		// Check content
		if (!$row->check())
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->addComponentMessage($row->getError(), 'error');
				$this->editfolderTask($row);
			}
			else
			{
				$response->success = 0;
				$response->message = $row->getError();
				echo json_encode($response);
			}
			return;
		}

		// Store new content
		if (!$row->store())
		{
			if (!$no_html && $tmpl != 'component')
			{
				$this->addComponentMessage($row->getError(), 'error');
				$this->editfolderTask($row);
			}
			else
			{
				$response->success = 0;
				$response->message = $row->getError();
				echo json_encode($response);
			}
			return;
		}

		if ($redirect)
		{
			if (!$no_html && $tmpl != 'component')
			{
				// Output messsage and redirect
				$this->setRedirect(
					'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
					JText::_('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_SAVED')
				);
				return;
			}

			/*$response->id       = $row->id;
			$response->title    = $row->title;
			$response->ordering = $row->ordering;
			$response->message  = JText::_('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_SAVED');

			echo json_encode($response);
			return;*/
			$this->listTask();
			return;
		}

		$this->editfolderTask($row);
	}

	/**
	 * Remove a folder
	 *
	 * @return  void
	 */
	public function removefolderTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		$ids = (is_array($ids) ?: array($ids));

		$no_html = JRequest::getInt('no_html', 0);

		foreach ($ids as $id)
		{
			$row = new SupportQuery($this->database);
			$row->deleteByFolder(intval($id));

			$row = new SupportTableQueryFolder($this->database);
			$row->delete(intval($id));
		}

		if (!$no_html)
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED')
			);
			return;
		}

		$this->listTask();
		/*$response = new stdClass;
		$response->success = 1;
		$response->message = JText::_('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED');

		echo json_encode($response);*/
	}

	/**
	 * Remove a folder
	 *
	 * @return  void
	 */
	public function saveorderingTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$folders = JRequest::getVar('folder', array());
		$queries = JRequest::getVar('queries', array());

		if (is_array($folders))
		{
			foreach ($folders as $key => $folder)
			{
				$row = new SupportTableQueryFolder($this->database);
				$row->load(intval($folder));
				$row->ordering = $key + 1;
				$row->store();
			}
		}

		if (is_array($queries))
		{
			$folder = null;
			$i = 0;

			foreach ($queries as $query)
			{
				$bits = explode('_', $query);

				$fd = intval($bits[0]);
				$id = intval($bits[1]);

				if ($fd != $folder)
				{
					$folder = $fd;
					$i = 0;
				}

				$row = new SupportQuery($this->database);
				$row->load($id);
				$row->folder_id = $fd;
				$row->ordering  = $i + 1;
				$row->store();

				$i++;
			}
		}

		if (!$no_html)
		{
			// Output messsage and redirect
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED')
			);
		}

		$response = new stdClass;
		$response->success = 1;
		$response->message = JText::_('COM_SUPPORT_QUERY_FOLDER_SUCCESSFULLY_REMOVED');

		echo json_encode($response);
	}
}
