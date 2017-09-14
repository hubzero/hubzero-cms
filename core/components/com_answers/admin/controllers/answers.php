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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Answers\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Answers\Models\Question;
use Components\Answers\Models\Response;
use Exception;
use Request;
use Notify;
use Config;
use Route;
use Lang;
use App;

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
		// Filters
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filterby',
				'state',
				-1,
				'int'
			),
			'question_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.qid',
				'qid',
				0,
				'int'
			),
			// Sorting
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$records = Response::all()
			->including(['creator', function ($creator){
				$creator->select('*');
			}]);

		$question = new Question;

		if ($filters['question_id'] > 0)
		{
			$question = Question::oneOrFail($filters['question_id']);

			$records->whereEquals('question_id', $filters['question_id']);
		}

		if ($filters['state'] >= 0)
		{
			$records->whereEquals('state', $filters['state']);
		}

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);

			$records->whereLike('answer', $filters['search'], 1)
				->resetDepth();
		}

		$rows = $records
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('question', $question)
			->display();
	}

	/**
	 * Displays a question response for editing
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = Request::getVar('id', array(0));
			$id = (is_array($id) && !empty($id)) ? $id[0] : $id;

			$row = Response::oneOrNew($id);
		}

		$qid = Request::getInt('qid', 0);
		$qid = $qid ?: $row->get('question_id');

		$question = Question::oneOrFail($qid);

		// Output the HTML
		$this->view
			->set('question', $question)
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
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields = Request::getVar('answer', array(), 'post', 'none', 2);

		// Initiate extended database class
		$row = Response::oneOrNew(intval($fields['id']))->set($fields);

		// Code cleaner
		$row->set('state', (isset($fields['state']) ? 1 : 0));
		$row->set('anonymous', (isset($fields['anonymous']) ? 1 : 0));

		// Trigger before save event
		$isNew  = $row->isNew();
		$result = Event::trigger('onAnswerBeforeSave', array(&$row, $isNew));

		if (in_array(false, $result, true))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Trigger after save event
		Event::trigger('onAnswerAfterSave', array(&$row, $isNew));

		// Display success message
		Notify::success(Lang::txt('COM_ANSWERS_ANSWER_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Removes one or more entries and associated data
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) <= 0)
		{
			return $this->cancelTask();
		}

		$success = 0;
		foreach ($ids as $id)
		{
			$ar = Response::oneOrFail(intval($id));

			if (!$ar->destroy())
			{
				Notify::error($ar->getError());
				continue;
			}

			// Trigger after delete event
			Event::trigger('onAnswerAfterDelete', array($id));

			$success++;
		}

		if ($success)
		{
			Notify::success(Lang::txt('COM_ANSWERS_ITEMS_REMOVED', $success));
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&qid=' . Request::getInt('qid', 0), false)
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
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$qid = Request::getInt('qid', 0);
		$id  = Request::getVar('id', array(0));
		$id  = !is_array($id) ? array($id) : $id;

		$publish = ($this->_task == 'accept') ? 1 : 0;

		// Check for an ID
		if (count($id) < 1)
		{
			$action = ($publish == 1) ? 'accept' : 'reject';

			Notify::warning(Lang::txt('COM_ANSWERS_ERROR_SELECT_ANSWER_TO', $action));
			return $this->cancelTask();
		}
		else if (count($id) > 1)
		{
			Notify::warning(Lang::txt('COM_ANSWERS_ERROR_ONLY_ONE_ACCEPTED_ANSWER'));
			return $this->cancelTask();
		}

		$ar = Response::oneOrFail($id[0]);

		/*if ($publish == 1)
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

		if (!$ar->save())
		{
			Notify::error($ar->getError());
			return $this->cancelTask();
		}*/

		if ($publish == 1)
		{
			if (!$ar->accept())
			{
				Notify::error($ar->getError());
				return $this->cancelTask();
			}
		}
		else
		{
			if (!$ar->reject())
			{
				Notify::error($ar->getError());
				return $this->cancelTask();
			}
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

		Notify::success($message);

		$this->cancelTask();
	}

	/**
	 * Reset the vote count for an entry
	 *
	 * @return  void
	 */
	public function resetTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$answer = Request::getVar('answer', array());

		// Reset some values
		$model = Response::oneOrFail(intval($answer['id']));

		if (!$model->reset())
		{
			Notify::error($ar->getError());
		}
		else
		{
			Notify::success(Lang::txt('COM_ANSWERS_VOTE_LOG_RESET'));
		}

		// Redirect
		$this->cancelTask();
	}
}
