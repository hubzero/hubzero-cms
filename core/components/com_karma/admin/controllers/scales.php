<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Karma\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Karma\Scale;
use Hubzero\Karma\Bands;
use Request;
use Notify;
use Route;
use Lang;
use User;
use App;

/**
 * Manage karma scales
 */
class Scales extends AdminController
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
	 * Display a list of scales
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => urldecode(Request::getState($this->_option . '.scales.search', 'search', '')),
			'sort'   => Request::getState($this->_option . '.scales.sort', 'filter_order', 'ordering'),
			'sort_Dir' => Request::getState($this->_option . '.scales.sortdir', 'filter_order_Dir', 'ASC')
		);

		$query = Scale::all();

		if ($filters['search'])
		{
			$query->whereLike('title', $filters['search'], 1)
			      ->orWhereLike('alias', $filters['search'], 1)
			      ->resetDepth();
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
	 * Edit a scale
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
			$row = Scale::oneOrNew($id);
		}

		// A sensible starting point: bounded either side of zero, described
		// in words rather than numbers, and hidden from everybody but the
		// subject until the hub decides otherwise.
		if ($row->isNew())
		{
			$row->set(array(
				'floor'             => -25,
				'ceiling'           => 50,
				'initial'           => 0,
				'visibility_self'   => Scale::SELF_ADJECTIVE,
				'visibility_public' => Scale::PUBLIC_HIDDEN,
				'adjectives'        => '-15=Restricted|-5=Provisional|0=Standing|10=Established|30=Trusted|99999=Distinguished',
				'state'             => 1
			));
		}

		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a scale
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

		$row = Scale::oneOrNew($fields['id'])->set($fields);

		if (!$this->validate($row))
		{
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

		// A scale's settings are cached per request by alias.
		Scale::forget();

		Notify::success(Lang::txt('COM_KARMA_SCALE_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Check the parts of a scale the model cannot check for itself
	 *
	 * @param   object  $row
	 * @return  bool
	 */
	protected function validate($row)
	{
		$valid = true;

		if ((float) $row->get('floor') >= (float) $row->get('ceiling'))
		{
			Notify::error(Lang::txt('COM_KARMA_SCALE_ERROR_BOUNDS'));
			$valid = false;
		}

		$initial = (float) $row->get('initial');

		if ($initial < (float) $row->get('floor') || $initial > (float) $row->get('ceiling'))
		{
			Notify::error(Lang::txt('COM_KARMA_SCALE_ERROR_INITIAL'));
			$valid = false;
		}

		if (trim($row->get('adjectives')) != '' && !Bands::parse($row->get('adjectives')))
		{
			Notify::error(Lang::txt('COM_KARMA_SCALE_ERROR_ADJECTIVES'));
			$valid = false;
		}

		return $valid;
	}

	/**
	 * Delete one or more scales
	 *
	 * A scale carrying history is refused rather than cascading: removing it
	 * would silently discard everybody's standing on it.
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

		$ids     = Request::getArray('id', array());
		$removed = 0;

		foreach ($ids as $id)
		{
			$row = Scale::oneOrFail((int) $id);

			if ($count = $row->ledgerCount())
			{
				Notify::error(Lang::txt('COM_KARMA_SCALE_ERROR_HAS_HISTORY', $row->get('title'), $count));
				continue;
			}

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$removed++;
		}

		Scale::forget();

		if ($removed)
		{
			Notify::success(Lang::txt('COM_KARMA_SCALES_REMOVED', $removed));
		}

		$this->cancelTask();
	}

	/**
	 * Publish or unpublish one or more scales
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

		$state = ($this->getTask() == 'publish' ? 1 : 0);
		$ids   = Request::getArray('id', array());

		foreach ($ids as $id)
		{
			$row = Scale::oneOrFail((int) $id);
			$row->set('state', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
			}
		}

		Scale::forget();

		Notify::success(Lang::txt($state ? 'COM_KARMA_SCALES_PUBLISHED' : 'COM_KARMA_SCALES_UNPUBLISHED', count($ids)));

		$this->cancelTask();
	}
}
