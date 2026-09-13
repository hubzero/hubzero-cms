<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Karma\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Karma\Karma as Reputation;
use Hubzero\Karma\Scale;
use Hubzero\Karma\Ledger;
use Hubzero\Karma\Balance;
use Hubzero\User\User as Account;
use Request;
use Pathway;
use Notify;
use Config;
use Route;
use Lang;
use User;
use App;

/**
 * A member's own karma
 */
class Karma extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'display');

		parent::execute();
	}

	/**
	 * Build the pathway
	 *
	 * @return  void
	 */
	protected function buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
	}

	/**
	 * Show what the member may do, then what their karma is
	 *
	 * Standing comes first and is shown whatever the scales say about
	 * visibility: if karma restricts what somebody may do, they are told what
	 * they may do. A number without its consequences is not feedback.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->buildPathway();

		$id = User::get('id');

		$scales  = Scale::all()->whereEquals('state', 1)->order('ordering', 'asc')->rows();
		$mine    = array();
		$account = Account::oneOrNew($id);

		foreach ($scales as $scale)
		{
			$balance = Balance::oneByUserAndScale($id, $scale->get('id'));

			$mine[] = array(
				'scale'      => $scale,
				'value'      => Reputation::describe($id, $scale->get('alias'), $id),
				'balance'    => $balance,
				'optional'   => ($scale->get('visibility_public') == Scale::PUBLIC_OPT_IN),
				'published'  => (bool) $account->getParam(Reputation::optInParam($scale), 0)
			);
		}

		$recent = Ledger::all()
			->whereEquals('subject_id', $id)
			->order('created', 'desc')
			->limit((int) $this->config->get('ledger_limit', 25))
			->rows();

		$this->view
			->set('standing', Reputation::standing($id))
			->set('mine', $mine)
			->set('recent', $recent)
			->set('config', $this->config)
			->display();
	}

	/**
	 * Full ledger for the logged-in member
	 *
	 * @return  void
	 */
	public function ledgerTask()
	{
		$this->buildPathway();

		Pathway::append(Lang::txt('COM_KARMA_LEDGER'), 'index.php?option=' . $this->_option . '&task=ledger');

		$rows = Ledger::all()
			->whereEquals('subject_id', User::get('id'))
			->order('created', 'desc')
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('rows', $rows)
			->setLayout('ledger')
			->display();
	}

	/**
	 * Save the member's opt-in decisions
	 *
	 * Only scales actually set to opt in are honoured. A scale the hub has
	 * hidden stays hidden whatever arrives in the request.
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		$id      = User::get('id');
		$account = Account::oneOrFail($id);
		$wanted  = Request::getArray('publish', array(), 'post');

		foreach (Scale::all()->whereEquals('state', 1)->rows() as $scale)
		{
			if ($scale->get('visibility_public') != Scale::PUBLIC_OPT_IN)
			{
				continue;
			}

			$account->setParam(
				Reputation::optInParam($scale),
				!empty($wanted[$scale->get('alias')]) ? 1 : 0
			);
		}

		$account->set('params', $account->params->toString());

		if (!$account->save())
		{
			Notify::error(Lang::txt('COM_KARMA_PREFERENCES_NOT_SAVED'));
		}
		else
		{
			Notify::success(Lang::txt('COM_KARMA_PREFERENCES_SAVED'));
		}

		App::redirect(Route::url('index.php?option=' . $this->_option));
	}
}
