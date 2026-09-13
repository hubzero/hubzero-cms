<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Karma\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Karma\Rule;
use Hubzero\Karma\Scale;
use Request;
use Notify;
use Event;
use Lang;
use User;
use App;

/**
 * Manage karma rules
 */
class Rules extends AdminController
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
	 * Display a list of rules, grouped by scale
	 *
	 * Rules with no plugin emitting them, and plugins emitting rules that do
	 * not exist, are both worth seeing: the first is dead configuration, the
	 * second is a component quietly earning nobody anything.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search'   => urldecode(Request::getState($this->_option . '.rules.search', 'search', '')),
			'scale'    => Request::getState($this->_option . '.rules.scale', 'scale', 0, 'int'),
			'sort'     => Request::getState($this->_option . '.rules.sort', 'filter_order', 'alias'),
			'sort_Dir' => Request::getState($this->_option . '.rules.sortdir', 'filter_order_Dir', 'ASC')
		);

		$query = Rule::all();

		if ($filters['search'])
		{
			$query->whereLike('title', $filters['search'], 1)
			      ->orWhereLike('alias', $filters['search'], 1)
			      ->resetDepth();
		}

		if ($filters['scale'])
		{
			$query->whereEquals('scale_id', $filters['scale']);
		}

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('scales', Scale::all()->order('ordering', 'asc')->rows())
			->set('declared', $this->declaredRules())
			->display();
	}

	/**
	 * Ask the karma plugins which rules they emit
	 *
	 * @return  array  alias => plugin name
	 */
	protected function declaredRules()
	{
		$declared = array();

		\Plugin::import('karma');

		$responses = Event::trigger('karma.onKarmaRules', array());

		foreach ((array) $responses as $response)
		{
			foreach ((array) $response as $rule)
			{
				if (!empty($rule['alias']))
				{
					$declared[$rule['alias']] = isset($rule['plugin']) ? $rule['plugin'] : '';
				}
			}
		}

		return $declared;
	}

	/**
	 * Edit a rule
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
			$row = Rule::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set(array('delta' => 1, 'state' => 1));
		}

		$this->view
			->set('row', $row)
			->set('scales', Scale::all()->order('ordering', 'asc')->rows())
			->set('declared', $this->declaredRules())
			->setLayout('edit')
			->display();
	}

	/**
	 * Save a rule
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

		$row = Rule::oneOrNew($fields['id'])->set($fields);

		if (!(float) $row->get('delta'))
		{
			Notify::error(Lang::txt('COM_KARMA_RULE_ERROR_DELTA'));
			return $this->editTask($row);
		}

		if (!(int) $row->get('scale_id'))
		{
			Notify::error(Lang::txt('COM_KARMA_RULE_ERROR_SCALE'));
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

		Rule::forget();

		Notify::success(Lang::txt('COM_KARMA_RULE_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Delete one or more rules
	 *
	 * Ledger entries record the rule alias as text, so history survives a
	 * deleted rule and explains itself without one.
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
			$row = Rule::oneOrFail((int) $id);

			if (!$row->destroy())
			{
				Notify::error($row->getError());
			}
		}

		Rule::forget();

		Notify::success(Lang::txt('COM_KARMA_RULES_REMOVED', count($ids)));

		$this->cancelTask();
	}

	/**
	 * Publish or unpublish one or more rules
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
			$row = Rule::oneOrFail((int) $id);
			$row->set('state', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
			}
		}

		Rule::forget();

		Notify::success(Lang::txt($state ? 'COM_KARMA_RULES_PUBLISHED' : 'COM_KARMA_RULES_UNPUBLISHED', count($ids)));

		$this->cancelTask();
	}
}
