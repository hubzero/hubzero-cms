<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Karma\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Karma\Gate;
use Hubzero\Karma\Scale;
use Hubzero\Karma\Bands;
use Request;
use Notify;
use Lang;
use User;
use App;

/**
 * Manage karma gates
 */
class Gates extends AdminController
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
	 * Display a list of gates
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'sort'     => Request::getState($this->_option . '.gates.sort', 'filter_order', 'alias'),
			'sort_Dir' => Request::getState($this->_option . '.gates.sortdir', 'filter_order_Dir', 'ASC')
		);

		$rows = Gate::all()
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('scales', Scale::all()->rows())
			->display();
	}

	/**
	 * Edit a gate
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
			$row = Gate::oneOrNew($id);
		}

		$this->view
			->set('row', $row)
			->set('scales', Scale::all()->order('ordering', 'asc')->rows())
			->set('preview', $this->preview($row))
			->setLayout('edit')
			->display();
	}

	/**
	 * Work out what this gate answers at each of its own boundaries
	 *
	 * Bands are easy to get subtly wrong — the rule is that the first
	 * threshold at or above the value wins — so the editor shows what the
	 * definition actually does rather than asking an administrator to
	 * simulate it in their head.
	 *
	 * @param   object  $row
	 * @return  array
	 */
	protected function preview($row)
	{
		$bands = Bands::parse($row->get('bands'));

		if (!$bands)
		{
			return array();
		}

		$preview    = array();
		$thresholds = array_keys($bands);

		foreach ($thresholds as $threshold)
		{
			$threshold = (float) $threshold;

			$preview[] = array(
				'karma' => $threshold,
				'value' => Bands::lookup($row->get('bands'), $threshold, $row->get('default_value')),
				'note'  => Lang::txt('COM_KARMA_GATE_PREVIEW_AT')
			);

			$preview[] = array(
				'karma' => $threshold + 1,
				'value' => Bands::lookup($row->get('bands'), $threshold + 1, $row->get('default_value')),
				'note'  => Lang::txt('COM_KARMA_GATE_PREVIEW_ABOVE')
			);
		}

		return $preview;
	}

	/**
	 * Save a gate
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

		$row = Gate::oneOrNew($fields['id'])->set($fields);

		if (trim($row->get('bands')) == '' || !Bands::parse($row->get('bands')))
		{
			Notify::error(Lang::txt('COM_KARMA_GATE_ERROR_BANDS'));
			return $this->editTask($row);
		}

		if (!$row->save())
		{
			foreach ($row->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $this->editTask($row);
		}

		Gate::forget();

		Notify::success(Lang::txt('COM_KARMA_GATE_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Delete one or more gates
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
			$row = Gate::oneOrFail((int) $id);

			if (!$row->destroy())
			{
				Notify::error($row->getError());
			}
		}

		Gate::forget();

		Notify::success(Lang::txt('COM_KARMA_GATES_REMOVED', count($ids)));

		$this->cancelTask();
	}
}
