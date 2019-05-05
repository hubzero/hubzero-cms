<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Components\Members\Models\Note;
use Components\Members\Models\Member;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Manage member notes
 */
class Notes extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		Lang::load($this->_option . '.notes', dirname(__DIR__));

		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display notes
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				0,
				'int'
			),
			'category_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.category_id',
				'category_id',
				0,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'review_time'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort_Dir',
				'filter_order_Dir',
				'DESC'
			)
		);

		$entries = Note::all()
			->including(['member', function ($member){
				$member->select('*');
			}])
			->including(['category', function ($category){
				$category
					->select('id')
					->select('title');
			}]);

		if ($filters['search'])
		{
			if (substr($filters['search'], 0, strlen('uid:')) == 'uid:')
			{
				$entries->whereEquals('user_id', (int)substr($filters['search'], strlen('uid:')));
			}
			else
			{
				$entries->whereLike('subject', strtolower((string)$filters['search']), 1)
					->orWhereLike('body', strtolower((string)$filters['search']), 1)
					->resetDepth();
			}
		}

		if ($filters['state'] >= 0)
		{
			$entries->whereEquals('state', (int)$filters['state']);
		}

		if ($filters['access'])
		{
			$entries->whereEquals('access', (int)$filters['access']);
		}

		if ($filters['category_id'])
		{
			$entries->whereEquals('catid', (int)$filters['category_id']);
		}

		$rows = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		if (!$row)
		{
			// Incoming
			$id = Request::getArray('id', array());

			// Get the single ID we're working with
			if (is_array($id))
			{
				$id = (!empty($id)) ? $id[0] : 0;
			}

			$row = Note::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('user_id', Request::getInt('user_id', 0));
			$row->set('catid', Request::getInt('category_id', 0));
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setErrors($this->getErrors())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming password blacklist edits
		$fields = Request::getArray('fields', array(), 'post');

		// Load the record
		$row = Note::oneOrNew($fields['id'])->set($fields);

		// Try to save
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_MEMBERS_NOTES_SAVE_SUCCESS'));

		// Fall through to edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		// Redirect
		$this->cancelTask();
	}

	/**
	 * Removes one or entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		// Do we have any IDs?
		if (!empty($ids))
		{
			// Loop through each ID and delete the necessary items
			foreach ($ids as $id)
			{
				$id = intval($id);

				// Remove the record
				$row = Note::oneOrFail($id);

				if (!$row->destroy())
				{
					Notify::error($row->getError());
					continue;
				}

				$i++;
			}
		}
		else // no rows were selected
		{
			Notify::warning(Lang::txt('COM_MEMBERS_NOTES_DELETE_NO_ROW_SELECTED'));
		}

		// Output messsage and redirect
		if ($i)
		{
			Notify::success(Lang::txt('COM_MEMBERS_NOTES_DELETE_SUCCESS'));
		}

		$this->cancelTask();
	}

	/**
	 * Display notes for a user
	 *
	 * @return  void
	 */
	public function modalTask()
	{
		Request::setVar('hidemainmenu', 1);

		$user = Member::oneOrFail(Request::getInt('id', 0));

		$rows = Note::all()
			->including(['category', function ($category){
				$category
					->select('id')
					->select('title');
			}])
			->whereEquals('user_id', (int)$user->get('id'))
			->ordered()
			->rows();

		// Output the HTML
		$this->view
			->set('user', $user)
			->set('rows', $rows)
			->setErrors($this->getErrors())
			->display();
	}
}
