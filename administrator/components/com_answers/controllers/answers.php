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
 * Controller class for question responses
 */
class AnswersControllerAnswers extends Hubzero_Controller
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
	 * Display all responses for a given question
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
		$this->view->filters['filterby'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.filterby',
			'filterby',
			'all'
		);
		$this->view->filters['qid']      = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.qid',
			'qid',
			0,
			'int'
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

		$this->view->question = new AnswersModelQuestion($this->view->filters['qid']);

		$ar = new AnswersResponse($this->database);

		// Get a record count
		$this->view->total = $ar->getCount($this->view->filters);

		// Get records
		$this->view->results = $ar->getResults($this->view->filters);

		// Did we get any results?
		if ($this->view->results)
		{
			foreach ($this->view->results as $key => $result)
			{
				$this->view->results[$key] = new AnswersModelResponse($result);
			}
		}

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
	 * Create a new response
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Displays a question response for editing
	 *
	 * @return	void
	 */
	public function editTask($row=null)
	{
		JRequest::setVar('hidemainmenu', 1);

		$this->view->setLayout('edit');

		// Incoming
		$id = 0;
		$qid = JRequest::getInt('qid', 0);
		$ids = JRequest::getVar('id', array());
		if (is_array($ids) && !empty($ids))
		{
			$id = $ids[0];
		}
		if (!$qid)
		{
			$qid = $id;
			$id = 0;
		}

		if (is_object($row))
		{
			$this->view->row = $row;
		}
		else 
		{
			// load infor from database
			$this->view->row = new AnswersModelResponse($id);
		}

		$this->view->question = new AnswersModelQuestion($qid);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save a response
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$answer = JRequest::getVar('answer', array(), 'post');
		$answer = array_map('trim', $answer);

		// Initiate extended database class
		$row = new AnswersResponse($this->database);
		if (!$row->bind($answer))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Code cleaner
		$row->answer = nl2br($row->answer);
		$row->created = $row->created ? $row->created : date("Y-m-d H:i:s");
		$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('username');
		$row->state = (isset($answer['state'])) ? 1 : 0;
		$row->anonymous = (isset($answer['anonymous'])) ? 1 : 0;

		// Check content
		if (!$row->check())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Store content
		if (!$row->store())
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Close the question if the answer is accepted
		if ($row->state == 1)
		{
			$aq = new AnswersQuestion($this->database);
			$aq->load($answer['qid']);
			$aq->state = 1;
			if (!$aq->store())
			{
				$this->addComponentMessage($aq->getError(), 'error');
				$this->editTask($row);
				return;
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Answer Successfully Saved')
		);
	}

	/**
	 * Removes one or more entries and associated data
	 * 
	 * @return     void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$qid = JRequest::getInt('qid', 0);
		$ids = JRequest::getVar('id', array());

		// Do we have any IDs?
		if (count($ids) > 0)
		{
			// Instantiate some objects
			$ar = new AnswersResponse($this->database);
			$al = new AnswersLog($this->database);

			// Loop through each ID
			foreach ($ids as $id)
			{
				if (!$ar->delete($id))
				{
					JError::raiseError(500, $ar->getError());
					return;
				}

				if (!$al->deleteLog($id))
				{
					JError::raiseError(500, $al->getError());
					return;
				}
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Mark an entry as "accepted" and unmark any previously accepted entry
	 * 
	 * @return     void
	 */
	public function acceptTask()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$qid = JRequest::getInt('qid', 0);
		$id = JRequest::getVar('id', array(0));

		if (!is_array($id))
		{
			$id = array(0);
		}

		$publish = ($this->_task == 'accept') ? 1 : 0;

		// Check for an ID
		if (count($id) < 1)
		{
			$action = ($publish == 1) ? 'accept' : 'unaccept';
			
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Select an answer to ' . $action),
				'error'
			);
			return;
		}
		else if (count($id) > 1)
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('A question can only have one accepted answer'),
				'error'
			);
			return;
		}

		$ar = new AnswersResponse($this->database);
		$ar->load($id[0]);
		$ar->state = $publish;
		if (!$ar->store())
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				$ar->getError(),
				'error'
			);
			return;
		}

		// Close the question if the answer is accepted
		$aq = new AnswersQuestion($this->database);
		$aq->load($qid);

		if ($publish == 1)
		{
			$aq->state = 1;
			$aq->reward = 0;

			if (!$aq->store())
			{
				JError::raiseError(500, $aq->getError());
				return;
			}

			if ($this->banking)
			{
				// Calculate and distribute earned points
				$AE = new AnswersEconomy($this->database);
				$AE->distribute_points($qid, $aq->created_by, $ar->created_by, 'closure');
			}

			$zuser =& JUser::getInstance($aq->created_by);

			// Load the plugins
			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();

			// Call the plugin
			if (!$dispatcher->trigger('onTakeAction', array('answers_reply_submitted', array($zuser->get('id')), $this->_option, $qid)))
			{
				$this->setError(JText::_('Failed to remove alert.'));
			}
		}
		else
		{
			$aq->state = 0;
			if (!$aq->store())
			{
				JError::raiseError(500, $aq->getError());
				return;
			}
		}

		// Set message
		if ($publish == '1')
		{
			$message = JText::_('Item successfully Accepted');
		}
		else if ($publish == '0')
		{
			$message = JText::_('Item successfully Unaccepted');
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
	public function cancelTask()
	{
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Reset the vote count for an entry
	 * 
	 * @return     void
	 */
	public function resetTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$answer = JRequest::getVar('answer', array());

		// Reset some values
		$ar = new AnswersResponse($this->database);
		$ar->load(intval($answer['id']));
		$ar->helpful = 0;
		$ar->nothelpful = 0;
		if (!$ar->store())
		{
			JError::raiseError(500, $ar->getError());
			return;
		}

		// Clear the history of "helpful" clicks
		$al = new AnswersLog($this->database);
		if (!$al->deleteLog(intval($answer['id'])))
		{
			JError::raiseError(500, $al->getError());
			return;
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Vote log has been reset.')
		);
	}
}

