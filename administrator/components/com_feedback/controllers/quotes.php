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
 * Feedback controller class for quotes
 */
class FeedbackControllerQuotes extends \Hubzero\Component\AdminController
{
	/**
	 * Display a list of quotes
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		if (JRequest::getMethod() == 'POST')
		{
			// Check for request forgeries
			JRequest::checkToken() or jexit('Invalid Token');
		}

		// Get site configuration
		$app = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['search'] = urldecode($app->getUserStateFromRequest(
			$this->_option . '.search',
			'search',
			''
		));

		// Get sorting variables
		$this->view->filters['sortby']     = trim($app->getUserStateFromRequest(
			$this->_option . '.sort',
			'filter_order',
			'date'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.sortdir',
			'filter_order_Dir',
			'DESC'
		));

		// Get paging variables
		$this->view->filters['start']  = $app->getUserStateFromRequest(
			$this->_option . '.limitstart',
			'limitstart',
			0,
			'int'
		);
		$this->view->filters['limit']  = $app->getUserStateFromRequest(
			$this->_option . '.limit',
			'limit',
			$config->getValue('config.list_limit'),
			'int'
		);

		$obj = new FeedbackQuotes($this->database);

		// Get a record count
		$this->view->total = $obj->getCount($this->view->filters);

		// Get records
		$this->view->rows = $obj->getResults($this->view->filters);

		// Initiate paging class
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
	 * Create a new entry
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
	 * @return     void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		if (JRequest::getMethod() == 'POST')
		{
			// Check for request forgeries
			JRequest::checkToken() or jexit('Invalid Token');
		}

		if (is_object($row))
		{
			$this->view->row = $row;
			$this->view->id  = $row->id;
		}
		else
		{
			// Incoming ID
			$id = JRequest::getVar('id', array(0));
			$id = (is_array($id) ? $id[0] : $id);

			// Initiate database class and load info
			$this->view->row = new FeedbackQuotes($this->database);
			$this->view->row->load($id);

			$this->view->id = $id;
		}

		$this->view->path = DS . trim($this->config->get('uploadpath', '/site/quotes'), DS) . DS;
		$path = JPATH_ROOT . $this->view->path . $id . DS;
		if (is_dir($path))
		{
			$pictures = scandir($path);
			array_shift($pictures);
			array_shift($pictures);
			$this->view->pictures = $pictures;
		}

		$username = trim(JRequest::getVar('username', ''));
		if ($username)
		{
			$profile = new \Hubzero\User\Profile();
			$profile->load($username);

			$this->view->row->fullname = $profile->get('name');
			$this->view->row->org      = $profile->get('organization');
			$this->view->row->user_id  = $profile->get('uidNumber');
		}

		if (!$id)
		{
			$this->view->row->date = JFactory::getDate()->toSql();
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
	 * Save an entry
	 *
	 * @return     void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save an entry
	 *
	 * @return     void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Initiate class and bind posted items to database fields
		$row = new FeedbackQuotes($this->database);
		$row->notable_quote = JRequest::getInt('notable_quotes', 0);

		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/quotes'), DS) . DS . $row->id;

		$existingPictures = scandir(JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/quotes'), DS) . DS . $row->id . DS);
		array_shift($existingPictures);
		array_shift($existingPictures);

		foreach ($existingPictures as $existingPicture)
		{
			if (!isset($_POST['existingPictures']) or in_array($existingPicture, $_POST['existingPictures']) === false)
			{
				if (!JFile::delete($path . DS . $existingPicture))
				{
					$this->setRedirect('index.php?option=' . $this->_option . '&controller=' . $this->_controller);
					return;
				}
			}

			if (count(scandir($path)) === 2)
			{
				rmdir($path);
			}
		}

		$files = $_FILES;

		if ($files['files']['name'][0] !== '')
		{
			if (is_dir($path) === false)
			{
				mkdir($path);
			}
			foreach ($files['files']['name'] as $fileIndex => $file)
			{
				JFile::upload($files['files']['tmp_name'][$fileIndex], $path . DS . $files['files']['name'][$fileIndex]);
			}
		}

		if (!$row->bind($_POST))
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Check new content
		if (!$row->check())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Store new content
		if (!$row->store())
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		if ($redirect)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::sprintf('COM_FEEDBACK_QUOTE_SAVED',  $row->fullname)
			);
		}

		$this->editTask($row);
	}

	/**
	 * Delete one or more entries
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for an ID
		if (!count($ids))
		{
			JError::raiseError(500, JText::_('COM_FEEDBACK_SELECT_QUOTE_TO_DELETE'));
			return;
		}

		$row = new FeedbackQuotes($this->database);

		foreach ($ids as $id)
		{
			// Delete the quote
			$row->delete(intval($id));
		}

		// Output messsage and redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&type=' . $this->type,
			JText::_('COM_FEEDBACK_REMOVED')
		);
	}

	/**
	 * Cancel a task and redirect to main listing
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&type=' . $this->type
		);
	}
}

