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

/**
 * Controller class for questions
 */
class AnswersControllerQuestions extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->banking = JComponentHelper::getParams('com_members')->get('bankAccounts');

		if ($this->banking)
		{
			ximport('Hubzero_Bank');
		}

		parent::execute();
	}

	/**
	 * List all questions
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Get Joomla configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Filters
		$this->view->filters = array();
		$this->view->filters['tag']      = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.tag',
			'tag',
			''
		);
		$this->view->filters['q']        = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.q',
			'q',
			''
		);
		$this->view->filters['filterby'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.filterby',
			'filterby',
			'all'
		);

		// Paging
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

		// Sorting
		$this->view->filters['sortby']   = '';
		$this->view->filters['sort']     = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort', 
			'filter_order', 
			'created'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir', 
			'filter_order_Dir', 
			'DESC'
		));

		$aq = new AnswersQuestion($this->database);

		// Get a record count
		$this->view->total = $aq->getCount($this->view->filters);

		// Get records
		$this->view->results = $aq->getResults($this->view->filters);

		// Did we get any results?
		if ($this->view->results)
		{
			foreach ($this->view->results as $key => $result)
			{
				$this->view->results[$key] = new AnswersModelQuestion($result);
			}
		}

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

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new question
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays a question for editing
	 *
	 * @return	void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$ids = JRequest::getVar('id', array(0));
		if (is_array($ids))
		{
			$id = $ids[0];
		}

		// Load object
		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			$this->view->row = new AnswersModelQuestion($id);
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
	 * Save a question
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming data
		$fields = JRequest::getVar('question', array(), 'post');

		// Initiate model
		$row = new AnswersModelQuestion($fields['id']);

		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Ensure we have at least one tag
		if (!isset($fields['tags']) || !$fields['tags'])
		{
			$this->addComponentMessage(JText::_('Question must have at least 1 tag'), 'error');
			$this->editTask($row);
			return;
		}

		$row->set('email', (isset($fields['email']) ? 1 : 0));
		$row->set('anonymous', (isset($fields['anonymous']) ? 1 : 0));

		// Store content
		if (!$row->store(true))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Add the tag(s)
		$row->tag($fields['tags'], $this->juser->get('id'), 1);

		// Redirect back to the full questions list
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Question Successfully Saved')
		);
	}

	/**
	 * Delete one or more questions and associated data
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		if (count($ids) <= 0)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Load the record
			$aq = new AnswersModelQuestion(intval($id));

			// Delete the question
			if (!$aq->delete())
			{
				$this->setError($aq->getError());
			}
		}

		// Redirect
		if ($this->getError())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				implode('<br />', $this->getErrors()),
				'error'
			);
			return;
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Question deleted')
		);
	}

	/**
	 * Set one or more questions to open
	 * 
	 * @return     void
	 */
	public function openTask()
	{
		$this->stateTask();
	}

	/**
	 * Set one or more questions to closed
	 * 
	 * @return     void
	 */
	public function closeTask()
	{
		$this->stateTask();
	}

	/**
	 * Set the state of one or more questions
	 * 
	 * @return     void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = JRequest::getVar('id', array());

		$publish = ($this->_task == 'close') ? 1 : 0;

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($publish == 1) ? JText::_('close') : JText::_('open');

			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Select a question to ' . $action),
				'error'
			);
			return;
		}

		// Load the plugins
		//JPluginHelper::importPlugin('xmessage');
		//$dispatcher =& JDispatcher::getInstance();

		foreach ($ids as $id)
		{
			// Update record(s)
			$aq = new AnswersModelQuestion(intval($id));
			if (!$aq->exists())
			{
				continue;
			}
			$aq->set('state', $publish);

			if ($publish == 1)
			{
				$aq->adjustCredits();
				/*// Call the plugin
				if (!$dispatcher->trigger('onTakeAction', array('answers_reply_submitted', array($aq->creator('id')), $this->_option, $id)))
				{
					$this->setError(JText::_('Failed to remove alert.'));
				}
				$aq->set('reward', 0);*/
			}

			if (!$aq->store())
			{
				JError::raiseError(500, $aq->getError());
				return;
			}
		}

		// set message
		if ($publish == 1)
		{
			$message = JText::_(count($ids) . ' Item(s) successfully Closed');
		}
		else if ($publish == 0)
		{
			$message = JText::_(count($ids) . ' Item(s) successfully Opened');
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message
		);
	}

	/**
	 * Cancel a task and redirect to default view
	 * 
	 * @return     void
	 */
	public function cancel()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}
}
