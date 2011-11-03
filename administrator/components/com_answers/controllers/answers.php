<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

ximport('Hubzero_Controller');

class AnswersControllerAnswers extends Hubzero_Controller
{
	public function execute()
	{
		$upconfig =& JComponentHelper::getParams('com_userpoints');
		$this->banking = $upconfig->get('bankAccounts');

		if ($this->banking) 
		{
			ximport('Hubzero_Bank');
		}

		parent::execute();
	}

	public function displayTask()
	{
		// Get Joomla configuration
		$config = JFactory::getConfig();
		$app =& JFactory::getApplication();

		// Filters
		$this->view->filters = array();
		$this->view->filters['limit']    = $app->getUserStateFromRequest(
			$this->_option . '.answers.limit', 
			'limit', 
			$config->getValue('config.list_limit'), 
			'int'
		);
		$this->view->filters['start']    = $app->getUserStateFromRequest(
			$this->_option . '.answers.limitstart', 
			'limitstart', 
			0, 
			'int'
		);
		$this->view->filters['filterby'] = $app->getUserStateFromRequest(
			$this->_option . '.answers.filterby', 
			'filterby', 
			'all'
		);
		$this->view->filters['sortby']   = $app->getUserStateFromRequest(
			$this->_option . '.answers.sortby', 
			'sortby', 
			'm.id DESC'
		);
		$this->view->filters['qid']      = $app->getUserStateFromRequest(
			$this->_option . '.answers.qid', 
			'qid', 
			0, 
			'int'
		);

		$this->view->question = new AnswersQuestion($this->database);
		$this->view->question->load($this->view->filters['qid']);

		$ar = new AnswersResponse($this->database);

		// Get a record count
		$this->view->total = $ar->getCount($this->view->filters);

		// Get records
		$this->view->results = $ar->getResults($this->view->filters);

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

		$this->view->qid = $this->view->filters['qid'];

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new ticket
	 *
	 * @return	void
	 */
	public function addTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Displays a question response for editing
	 *
	 * @return	void
	 */
	public function editTask()
	{
		// Incoming
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

		// load infor from database
		$this->view->row = new AnswersResponse($this->database);
		$this->view->row->load($id);

		if ($this->_task == 'add') 
		{
			$this->view->row->answer     = '';
			$this->view->row->created    = date('Y-m-d H:i:s', time());
			$this->view->row->created_by = $this->juser->get('username');
			$this->view->row->qid        = $qid;
			$this->view->row->helpful    = 0;
			$this->view->row->nothelpful = 0;
		} 
		else 
		{
			$this->view->row->answer = AnswersHtml::unpee($this->view->row->answer);
		}

		$this->view->question = new AnswersQuestion($this->database);
		$this->view->question->load($qid);
		$this->view->qid = $qid;

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	//-----------

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
			JError::raiseError(500, $row->getError());
			return;
		}

		// Code cleaner
		$row->answer = nl2br($row->answer);
		$row->created = $row->created ? $row->created : date("Y-m-d H:i:s");
		$row->created_by = $row->created_by ? $row->created_by : $this->juser->get('username');
		$row->state = (isset($answer['state'])) ? 1 : 0;

		// Check content
		if (!$row->check()) 
		{
			JError::raiseError(500, $row->getError());
			return;
		}

		// Store content
		if (!$row->store()) 
		{
			JError::raiseError(500, $row->getError());
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
				JError::raiseError(500, $aq->getError());
				return;
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
		$this->_message = JText::_('Answer Successfully Saved');
	}

	//-----------

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
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}

	//-----------
	
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
			echo AnswersHtml::alert(JText::_('Select an answer to ' . $action));
			exit;
		} 
		else if (count($id) > 1) 
		{
			echo AnswersHtml::alert(JText::_('A question can only have one accepted answer'));
			exit;
		}

		$ar = new AnswersResponse($this->database);
		$ar->load($id[0]);
		$ar->state = $publish;
		if (!$ar->store()) 
		{
			JError::raiseError(500, $ar->getError());
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
			$this->_message = JText::_('Item successfully Accepted');
		} 
		else if ($publish == '0') 
		{
			$this->_message = JText::_('Item successfully Unaccepted');
		}

		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}

	//-----------

	public function cancelTask()
	{
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}

	//-----------

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
		$this->_redirect = 'index.php?option=' . $this->_option . '&controller=' . $this->_controller;
	}
}

