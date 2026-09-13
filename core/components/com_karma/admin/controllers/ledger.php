<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Karma\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Karma\Ledger as Entry;
use Hubzero\Karma\Scale;
use Hubzero\Karma\Karma;
use Components\Members\Models\Member;
use Request;
use Notify;
use Lang;
use User;
use App;

/**
 * Browse the karma ledger
 *
 * The abuse-investigation tool. Read-only except for reversing an entry,
 * because the ledger is append-only by design: an entry is marked reversed
 * and compensated, never edited away.
 */
class Ledger extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!User::authorise('karma.viewledger', $this->_option)
		 && !User::authorise('core.admin', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		parent::execute();
	}

	/**
	 * Display the ledger
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'subject'  => Request::getState($this->_option . '.ledger.subject', 'subject', 0, 'int'),
			'actor'    => Request::getState($this->_option . '.ledger.actor', 'actor', 0, 'int'),
			'rule'     => urldecode(Request::getState($this->_option . '.ledger.rule', 'rule', '')),
			'scale'    => Request::getState($this->_option . '.ledger.scale', 'scale', 0, 'int'),
			'source'   => urldecode(Request::getState($this->_option . '.ledger.source', 'source', '')),
			'state'    => Request::getState($this->_option . '.ledger.state', 'state', '-1'),
			'from'     => urldecode(Request::getState($this->_option . '.ledger.from', 'from', '')),
			'to'       => urldecode(Request::getState($this->_option . '.ledger.to', 'to', '')),
			'sort'     => Request::getState($this->_option . '.ledger.sort', 'filter_order', 'created'),
			'sort_Dir' => Request::getState($this->_option . '.ledger.sortdir', 'filter_order_Dir', 'DESC')
		);

		$query = Entry::all();

		if ($filters['subject'])
		{
			$query->whereEquals('subject_id', $filters['subject']);
		}

		if ($filters['actor'])
		{
			$query->whereEquals('actor_id', $filters['actor']);
		}

		if ($filters['rule'])
		{
			$query->whereEquals('rule', $filters['rule']);
		}

		if ($filters['scale'])
		{
			$query->whereEquals('scale_id', $filters['scale']);
		}

		if ($filters['source'])
		{
			$query->whereLike('source_type', $filters['source']);
		}

		if ($filters['state'] != '-1' && $filters['state'] !== '')
		{
			$query->whereEquals('state', (int) $filters['state']);
		}

		if ($filters['from'])
		{
			$query->where('created', '>=', $filters['from'] . ' 00:00:00');
		}

		if ($filters['to'])
		{
			$query->where('created', '<=', $filters['to'] . ' 23:59:59');
		}

		$rows = $query
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->set('scales', Scale::all()->rows())
			->set('rules', $this->distinctRules())
			->display();
	}

	/**
	 * The rule aliases that actually appear in the ledger
	 *
	 * Taken from the ledger rather than the rules table, so a rule that has
	 * since been deleted is still filterable — which is exactly when an
	 * investigation needs it.
	 *
	 * @return  array
	 */
	protected function distinctRules()
	{
		$rows = Entry::all()
			->select('rule')
			->group('rule')
			->order('rule', 'asc')
			->rows();

		$rules = array();

		foreach ($rows as $row)
		{
			if ($row->get('rule'))
			{
				$rules[] = $row->get('rule');
			}
		}

		return $rules;
	}

	/**
	 * Reverse one ledger entry
	 *
	 * @return  void
	 */
	public function reverseTask()
	{
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids      = Request::getArray('id', array());
		$reversed = 0;

		foreach ($ids as $id)
		{
			$entry = Entry::oneOrFail((int) $id);

			if (!$entry->isActive())
			{
				continue;
			}

			$reversed += Karma::revoke(
				$entry->get('source_type'),
				$entry->get('source_id'),
				$entry->get('rule'),
				$entry->get('subject_id')
			);
		}

		if ($reversed)
		{
			Notify::success(Lang::txt('COM_KARMA_LEDGER_REVERSED', $reversed));
		}
		else
		{
			Notify::warning(Lang::txt('COM_KARMA_LEDGER_NOTHING_REVERSED'));
		}

		$this->cancelTask();
	}
}
