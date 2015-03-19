<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Answers\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Answers\Models\Question;
use Components\Answers\Models\Response;
use Components\Answers\Tables;
use Exception;

/**
 * Controller class for question responses
 */
class Answers extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->banking = \Component::params('com_members')->get('bankAccounts');

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('reject', 'accept');

		parent::execute();
	}

	/**
	 * Display all responses for a given question
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get Joomla configuration
		$config = \JFactory::getConfig();
		$app = \JFactory::getApplication();

		// Filters
		$this->view->filters = array(
			'filterby' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.filterby',
				'filterby',
				'all'
			),
			'question_id' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.qid',
				'qid',
				0,
				'int'
			),
			// Paging
			'limit' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				$config->getValue('config.list_limit'),
				'int'
			),
			'start' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Sorting
			'sortby' => '',
			'sort' => trim($app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			)),
			'sort_Dir' => trim($app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			))
		);

		$this->view->question = new Question($this->view->filters['question_id']);

		$ar = new Tables\Response($this->database);

		// Get a record count
		$this->view->total   = $ar->find('count', $this->view->filters);

		// Get records
		$this->view->results = $ar->find('list', $this->view->filters);

		// Did we get any results?
		if ($this->view->results)
		{
			foreach ($this->view->results as $key => $result)
			{
				$this->view->results[$key] = new Response($result);
			}
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Displays a question response for editing
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		\JRequest::setVar('hidemainmenu', 1);

		// Incoming
		$qid = \JRequest::getInt('qid', 0);

		if (!is_object($row))
		{
			$id = \JRequest::getVar('id', array(0));
			$id = (is_array($id) && !empty($id)) ? $id[0] : $id;

			$row = new Response($id);
		}

		$qid = $qid ?: $row->get('question_id');

		$this->view->set('question', new Question($qid));

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a response
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$answer = \JRequest::getVar('answer', array(), 'post', 'none', 2);

		// Initiate extended database class
		$row = new Response(intval($answer['id']));
		if (!$row->bind($answer))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Code cleaner
		$row->set('state', (isset($answer['state']) ? 1 : 0));
		$row->set('anonymous', (isset($answer['anonymous']) ? 1 : 0));

		// Store content
		if (!$row->store(true))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_ANSWERS_ANSWER_SAVED')
		);
	}

	/**
	 * Removes one or more entries and associated data
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = \JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we have any IDs?
		if (count($ids) > 0)
		{
			// Loop through each ID
			foreach ($ids as $id)
			{
				$ar = new Response(intval($id));
				if (!$ar->delete())
				{
					throw new Exception($ar->getError(), 500);
				}
			}
		}

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&qid=' . JRequest::getInt('qid', 0), false)
		);
	}

	/**
	 * Mark an entry as "accepted" and unmark any previously accepted entry
	 *
	 * @return  void
	 */
	public function acceptTask()
	{
		// Check for request forgeries
		\JRequest::checkToken('get') or \JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$qid = \JRequest::getInt('qid', 0);
		$id  = \JRequest::getVar('id', array(0));

		if (!is_array($id))
		{
			$id = array($id);
		}

		$publish = ($this->getTask() == 'accept') ? 1 : 0;

		// Check for an ID
		if (count($id) < 1)
		{
			$action = ($publish == 1) ? 'accept' : 'reject';

			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_ANSWERS_ERROR_SELECT_ANSWER_TO', $action),
				'error'
			);
			return;
		}
		else if (count($id) > 1)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_ANSWERS_ERROR_ONLY_ONE_ACCEPTED_ANSWER'),
				'error'
			);
			return;
		}

		$ar = new Response($id[0]);
		if (!$ar->exists())
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		if ($publish == 1)
		{
			// Unmark all other entries
			$tbl = new Tables\Response($this->database);
			if ($results = $tbl->find('list', array('question_id' => $ar->get('question_id'))))
			{
				foreach ($results as $result)
				{
					$result = new Response($result);
					if ($result->get('state') != 0 && $result->get('state') != 1)
					{
						continue;
					}
					$result->set('state', 0);
					$result->store(false);
				}
			}
		}

		// Mark this entry
		$ar->set('state', $publish);
		if (!$ar->store(false))
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				$ar->getError(),
				'error'
			);
			return;
		}

		// Set message
		if ($publish == '1')
		{
			$message = Lang::txt('COM_ANSWERS_ANSWER_ACCEPTED');
		}
		else if ($publish == '0')
		{
			$message = Lang::txt('COM_ANSWERS_ANSWER_REJECTED');
		}

		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$message
		);
	}

	/**
	 * Reset the vote count for an entry
	 *
	 * @return  void
	 */
	public function resetTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$answer = \JRequest::getVar('answer', array());

		// Reset some values
		$model = new Response(intval($answer['id']));

		if (!$model->reset())
		{
			throw new Exception($ar->getError(), 500);
		}

		// Redirect
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_ANSWERS_VOTE_LOG_RESET')
		);
	}
}

