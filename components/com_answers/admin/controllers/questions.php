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
use Components\Answers\Tables;
use Exception;

/**
 * Controller class for questions
 */
class Questions extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->banking = \JComponentHelper::getParams('com_members')->get('bankAccounts');

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('open', 'state');
		$this->registerTask('close', 'state');

		parent::execute();
	}

	/**
	 * List all questions
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
			'tag' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.tag',
				'tag',
				''
			),
			'q' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.q',
				'q',
				''
			),
			'filterby' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.filterby',
				'filterby',
				'all'
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
			'sort' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => $app->getUserStateFromRequest(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$aq = new Tables\Question($this->database);

		// Get a record count
		$this->view->total = $aq->getCount($this->view->filters);

		// Get records
		$this->view->results = $aq->getResults($this->view->filters);

		// Did we get any results?
		if ($this->view->results)
		{
			foreach ($this->view->results as $key => $result)
			{
				$this->view->results[$key] = new Question($result);
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
	 * Displays a question for editing
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		\JRequest::setVar('hidemainmenu', 1);

		// Load object
		if (!is_object($row))
		{
			// Incoming
			$id = \JRequest::getVar('id', array(0));
			$id = is_array($id) ? $id[0] : $id;

			$row = new Question($id);
		}

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
	 * Save a question
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		\JRequest::checkToken() or jexit('Invalid Token');

		// Incoming data
		$fields = \JRequest::getVar('question', array(), 'post', 'none', 2);

		// Initiate model
		$row = new Question($fields['id']);

		if (!$row->bind($fields))
		{
			$this->addComponentMessage($row->getError(), 'error');
			$this->editTask($row);
			return;
		}

		// Ensure we have at least one tag
		if (!isset($fields['tags']) || !$fields['tags'])
		{
			$this->addComponentMessage(Lang::txt('COM_ANSWERS_ERROR_QUESTION_MUST_HAVE_TAGS'), 'error');
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
		$row->tag($fields['tags'], $this->juser->get('id'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect back to the full questions list
		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_ANSWERS_QUESTION_SAVED')
		);
	}

	/**
	 * Delete one or more questions and associated data
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

		if (count($ids) <= 0)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Load the record
			$aq = new Question(intval($id));

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
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				implode('<br />', $this->getErrors()),
				'error'
			);
			return;
		}

		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_ANSWERS_QUESTION_DELETED')
		);
	}

	/**
	 * Set the state of one or more questions
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		\JRequest::checkToken('get') or \JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$ids = \JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$publish = ($this->getTask() == 'close') ? 1 : 0;

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($publish == 1) ? Lang::txt('COM_ANSWERS_SET_STATE_CLOSE') : Lang::txt('COM_ANSWERS_SET_STATE_OPEN');

			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_ANSWERS_ERROR_SELECT_QUESTION_TO', $action),
				'error'
			);
			return;
		}

		foreach ($ids as $id)
		{
			// Update record(s)
			$aq = new Question(intval($id));
			if (!$aq->exists())
			{
				continue;
			}
			$aq->set('state', $publish);

			if ($publish == 1)
			{
				$aq->adjustCredits();
			}

			if (!$aq->store())
			{
				throw new Exception($aq->getError(), 500);
			}
		}

		// Set message
		if ($publish == 1)
		{
			$message = Lang::txt('COM_ANSWERS_QUESTIONS_CLOSED', count($ids));
		}
		else if ($publish == 0)
		{
			$message = Lang::txt('COM_ANSWERS_QUESTIONS_OPENED', count($ids));
		}

		$this->setRedirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$message
		);
	}
}
