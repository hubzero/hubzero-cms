<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Story\Models\Section;
use Hubzero\Config\Registry;
use Request;
use Notify;
use Lang;
use User;
use App;

/**
 * Manage sections
 */
class Sections extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * List them
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search'   => urldecode(Request::getState($this->_option . '.sections.search', 'search', '')),
			'sort'     => Request::getState($this->_option . '.sections.sort', 'filter_order', 'ordering'),
			'sort_Dir' => Request::getState($this->_option . '.sections.sortdir', 'filter_order_Dir', 'ASC')
		);

		$query = Section::all();

		if ($filters['search'])
		{
			$query->whereLike('title', $filters['search']);
		}

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Edit one
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id  = Request::getArray('id', array(0));
			$id  = (is_array($id) ? $id[0] : $id);
			$row = Section::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('state', Section::STATE_PUBLISHED);
		}

		$this->view
			->set('row', $row)
			->set('all', Section::all()->order('ordering', 'asc')->rows())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save one
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$fields = Request::getArray('fields', array(), 'post');

		$row = Section::oneOrNew($fields['id'])->set($fields);

		// Params arrive as their own array so a form that knows about one
		// setting cannot wipe the others.
		$params = Request::getArray('params', array(), 'post');

		if ($params)
		{
			$merged = new Registry($row->get('params'));

			foreach ($params as $key => $value)
			{
				$merged->set($key, $value);
			}

			$row->set('params', $merged->toString());
		}

		if (!$row->save())
		{
			foreach ($row->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_STORY_SECTION_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Publish or unpublish
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = ($this->getTask() == 'publish') ? Section::STATE_PUBLISHED : Section::STATE_UNPUBLISHED;
		$ids   = Request::getArray('id', array());

		foreach ($ids as $id)
		{
			$row = Section::oneOrFail((int) $id);
			$row->set('state', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
			}
		}

		Notify::success(Lang::txt('COM_STORY_STATE_CHANGED', count($ids)));

		$this->cancelTask();
	}

	/**
	 * Delete
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());

		foreach ($ids as $id)
		{
			$row = Section::oneOrFail((int) $id);

			if (!$row->destroy())
			{
				Notify::error($row->getError());
			}
		}

		Notify::success(Lang::txt('COM_STORY_REMOVED', count($ids)));

		$this->cancelTask();
	}
}
