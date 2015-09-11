<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Answers\Models\Question;
use Components\Answers\Tables;
use Exception;
use Request;
use Config;
use Notify;
use Route;
use Lang;
use App;

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
		$this->banking = \Component::params('com_members')->get('bankAccounts');

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
		// Filters
		$this->view->filters = array(
			'tag' => Request::getstate(
				$this->_option . '.' . $this->_controller . '.tag',
				'tag',
				''
			),
			'q' => Request::getstate(
				$this->_option . '.' . $this->_controller . '.q',
				'q',
				''
			),
			'filterby' => Request::getstate(
				$this->_option . '.' . $this->_controller . '.filterby',
				'filterby',
				'all'
			),
			// Paging
			'limit' => Request::getstate(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getstate(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
				'int'
			),
			// Sorting
			'sortby' => '',
			'sort' => Request::getstate(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => Request::getstate(
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
		Request::setVar('hidemainmenu', 1);

		// Load object
		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			$id = is_array($id) ? $id[0] : $id;

			$row = new Question($id);
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
		Request::checkToken();

		// Incoming data
		$fields = Request::getVar('question', array(), 'post', 'none', 2);

		// Initiate model
		$row = new Question($fields['id']);

		if (!$row->bind($fields))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Ensure we have at least one tag
		if (!isset($fields['tags']) || !$fields['tags'])
		{
			Notify::error(Lang::txt('COM_ANSWERS_ERROR_QUESTION_MUST_HAVE_TAGS'));
			return $this->editTask($row);
		}

		$row->set('email', (isset($fields['email']) ? 1 : 0));
		$row->set('anonymous', (isset($fields['anonymous']) ? 1 : 0));

		// Store content
		if (!$row->store(true))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Add the tag(s)
		$row->tag($fields['tags'], User::get('id'));

		Notify::success(Lang::txt('COM_ANSWERS_QUESTION_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect back to the full questions list
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
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
		Request::checkToken();

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			App::redirect(
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
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				implode('<br />', $this->getErrors()),
				'error'
			);
			return;
		}

		App::redirect(
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
		Request::checkToken(['get', 'post']);

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$publish = ($this->getTask() == 'close') ? 1 : 0;

		// Check for an ID
		if (count($ids) < 1)
		{
			$action = ($publish == 1) ? Lang::txt('COM_ANSWERS_SET_STATE_CLOSE') : Lang::txt('COM_ANSWERS_SET_STATE_OPEN');

			App::redirect(
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

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$message
		);
	}
}
