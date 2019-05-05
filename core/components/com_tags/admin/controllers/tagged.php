<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tags\Admin\Controllers;

use Components\Tags\Models\Objct;
use Hubzero\Component\AdminController;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Tags controller class for listing tagged objects
 */
class Tagged extends AdminController
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

		parent::execute();
	}

	/**
	 * List all tagged objects
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$filters = array(
			'tagid' => Request::getState(
				$this->_option . '.' . $this->_controller . '.tag',
				'tag',
				0,
				'int'
			),
			'tbl'     => Request::getState(
				$this->_option . '.' . $this->_controller . '.tbl',
				'tbl',
				''
			),
			'sort'     => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'raw_tag'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$modelt = Objct::all()
			->select('DISTINCT(tbl)');

		$model = Objct::all();

		if ($filters['tagid'])
		{
			$model->whereEquals('tagid', $filters['tagid']);
			$modelt->whereEquals('tagid', $filters['tagid']);
		}

		if ($filters['tbl'])
		{
			$model->whereEquals('tbl', $filters['tbl']);
		}

		// Get records
		$rows = $model
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		$types = $modelt
			->ordered()
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->set('types', $types)
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
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		// Load a tag object if one doesn't already exist
		if (!is_object($row))
		{
			// Incoming
			$id = Request::getArray('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$row = Objct::oneOrNew(intval($id));
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
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

		$row = Objct::oneOrFail($fields['id'])->set($fields);

		// Store content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_TAGS_OBJECT_SAVED'));

		// Redirect to main listing
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
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Make sure we have an ID
		if (empty($ids))
		{
			Notify::warning(Lang::txt('COM_TAGS_ERROR_NO_ITEMS_SELECTED'));

			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			// Remove entry
			$row = Objct::oneOrFail(intval($id));

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_TAGS_OBJECT_REMOVED'));
		}

		$this->cancelTask();
	}
}
