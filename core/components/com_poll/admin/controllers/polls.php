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

namespace Components\Poll\Admin\Controllers;

use Components\Poll\Models\Poll;
use Components\Poll\Models\Option;
use Hubzero\Component\AdminController;
use Hubzero\Utility\Arr;
use Exception;
use stdClass;
use Request;
use Notify;
use User;
use Lang;
use App;

/**
 * Controller class for polls
 */
class Polls extends AdminController
{
	/**
	 * Constructor
	 *
	 * @param   array  $config  Optional configurations to be used
	 * @return  void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('unpublish', 'publish');
		$this->registerTask('close', 'open');
		$this->registerTask('apply', 'save');
		$this->registerTask('add', 'edit');
	}

	/**
	 * Display a list of polls
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filter_state',
				'filter_state',
				'',
				'word'
			),
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				'',
				'string'
			),
			'order'  => Request::getState(
				$this->_option . '.' . $this->_controller . '.filter_order',
				'filter_order',
				'm.id',
				'cmd'
			),
			'order_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.filter_order_Dir',
				'filter_order_Dir',
				'',
				'word'
			)
		);

		$polls = Poll::all()
			->including(['options', function ($option){
				$option
					->select('id')
					->select('poll_id')
					->where('text', '<>', '');
			}])
			->including(['dates', function ($date){
				$date
					->select('id')
					->select('poll_id');
			}]);

		if ($filters['search'])
		{
			if (strpos($filters['search'], '"') !== false)
			{
				$filters['search'] = str_replace(array('=', '<'), '', $filters['search']);
			}
			$filters['search'] = strtolower($filters['search']);

			$polls->whereLike('title', strtolower((string)$filters['search']));
		}

		if ($filters['state'])
		{
			if ($filters['state'] == 'P')
			{
				$polls->whereEquals('state', 1);
			}
			else if ($filters['state'] == 'U')
			{
				$polls->whereEquals('state', 0);
			}
		}

		$rows = $polls
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		$filters['states'] = \Html::grid('states', $filters['state']);

		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Preview a poll
	 *
	 * @return  void
	 */
	public function previewTask()
	{
		Request::setVar('hidemainmenu', 1);
		Request::setVar('tmpl', 'component');

		$id = Request::getVar('id', array(0));
		if (is_array($id) && !empty($id))
		{
			$id = $id[0];
		}

		$poll = Poll::oneOrFail($id);

		$options = $poll->options()
			->ordered()
			->rows();

		$this->view
			->set('poll', $poll)
			->set('options', $options)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $poll  Poll object
	 * @return  void
	 */
	public function editTask($poll=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!is_object($poll))
		{
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$poll = Poll::oneOrNew($id);
		}

		// Fail if checked out not by 'me'
		if ($poll->isCheckedOut(User::get('id')))
		{
			Notify::warning(Lang::txt('DESCBEINGEDITTED', Lang::txt('The poll'), $poll->get('title')));

			return $this->cancelTask();
		}

		if ($poll->isNew())
		{
			$poll->published = 1;
		}

		$poll->checkout(User::get('id'));

		$options = $poll->options()
			->ordered()
			->rows();

		// Output the HTML
		$this->view
			->set('poll', $poll)
			->set('options', $options)
			->setLayout('edit')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$fields = Request::getVar('fields', array(), 'post');

		// Save the poll parent information
		$row = Poll::oneOrNew($fields['id'])->set($fields);

		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		$row->checkin();

		// Save the poll options
		$options = Request::getVar('polloption', array(), 'post');

		foreach ($options as $i => $text)
		{
			$option = new Option;
			$option->set('pollid', (int) $row->get('id'));
			$option->set('text', htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));

			if ($fields['id'])
			{
				$option->set('id', (int) $i);
			}

			if (!$option->save())
			{
				Notify::error($option->getError());
				return $this->editTask($row);
			}
		}

		Notify::success(Lang::txt('COM_POLL_ITEM_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$ids = Request::getVar('id', array());
		Arr::toInteger($ids);

		foreach ($ids as $id)
		{
			$poll = Poll::oneOrFail(intval($id));

			if (!$poll->destroy())
			{
				Notify::error($poll->getError());
			}
		}

		$this->cancelTask();
	}

	/**
	* Publishes or Unpublishes one or more records
	*
	* @return  void
	*/
	public function publishTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$ids = Request::getVar('id', array());
		Arr::toInteger($ids);

		$state = (Request::getVar('task') == 'publish' ? 1 : 0);

		if (count($ids) < 1)
		{
			$action = $publish ? 'COM_POLL_PUBLISH' : 'COM_POLL_UNPUBLISH';

			Notify::warning(Lang::txt('COM_POLL_SELECT_ITEM_TO', Lang::txt($action), true));

			return $this->cancelTask();
		}

		foreach ($ids as $id)
		{
			$poll = Poll::oneOrFail(intval($id));

			if ($poll->get('checked_out') && $poll->get('checked_out') != User::get('id'))
			{
				continue;
			}

			$poll->set('state', (int) $state);

			if (!$poll->save())
			{
				Notify::error($poll->getError());
			}
		}

		$this->cancelTask();
	}

	/**
	* Mark a poll as open or closed
	*
	* @return  void
	*/
	public function openTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$ids = Request::getVar('id', array());
		Arr::toInteger($ids);

		$publish = (Request::getVar('task') == 'open' ? 1 : 0);

		if (count($ids) < 1)
		{
			$action = $publish ? 'COM_POLL_OPEN' : 'COM_POLL_CLOSE';

			Notify::warning(Lang::txt('COM_POLL_SELECT_ITEM_TO', Lang::txt($action), true));

			return $this->cancelTask();
		}

		foreach ($ids as $id)
		{
			$poll = Poll::oneOrFail(intval($id));

			if ($poll->get('checked_out') && $poll->get('checked_out') != User::get('id'))
			{
				continue;
			}

			$poll->set('open', (int) $publish);

			if (!$poll->save())
			{
				Notify::error($poll->getError());
			}
		}

		$this->cancelTask();
	}

	/**
	 * Cancels a task and redirects to listing
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if ($id  = Request::getVar('id', 0, '', 'int'))
		{
			$row = Poll::oneOrFail($id);
			$row->checkin();
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}
}