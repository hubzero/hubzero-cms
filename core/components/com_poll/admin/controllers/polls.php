<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
				'id',
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

		$id = Request::getArray('id', array(0));
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
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($poll))
		{
			$id = Request::getArray('id', array(0));
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
			$poll->set('published', 1);
		}
		else
		{
			$poll->checkout(User::get('id'));
		}

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

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$fields = Request::getArray('fields', array(), 'post');

		// Save the poll parent information
		$row = Poll::oneOrNew($fields['id'])->set($fields);

		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		$row->checkin();

		// Save the poll options
		$options = Request::getArray('polloption', array(), 'post');

		foreach ($options as $i => $text)
		{
			$option = new Option;
			$option->set('poll_id', (int) $row->get('id'));
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

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());
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

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$state = (Request::getCmd('task') == 'publish' ? 1 : 0);

		if (empty($ids))
		{
			$action = $state ? 'COM_POLL_PUBLISH' : 'COM_POLL_UNPUBLISH';

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

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$publish = ($this->getTask() == 'open' ? Poll::STATE_PUBLISHED : Poll::STATE_UNPUBLISHED);

		if (empty($ids))
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
		if ($id = Request::getInt('id', 0, '', 'int'))
		{
			if (is_int($id))
			{
				$row = Poll::oneOrFail($id);
				$row->checkin();
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option, false)
		);
	}
}
