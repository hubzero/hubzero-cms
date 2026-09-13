<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Karma\Scale;
use Hubzero\Karma\Ledger;
use Hubzero\Karma\Balance;
use Hubzero\Karma\Karma;
use Hubzero\Utility\Date;

/**
 * Cron plugin for karma
 */
class plgCronKarma extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  object
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'decayKarma',
				'label'  => Lang::txt('PLG_CRON_KARMA_DECAY'),
				'params' => ''
			),
			array(
				'name'   => 'expireKarma',
				'label'  => Lang::txt('PLG_CRON_KARMA_EXPIRE'),
				'params' => ''
			),
			array(
				'name'   => 'recalculateKarma',
				'label'  => Lang::txt('PLG_CRON_KARMA_RECALCULATE'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Move idle members' karma back toward where they started
	 *
	 * Writes a ledger row for every move, so a balance that drifted downward
	 * can still explain why. Decay that left no trace would make the ledger a
	 * lie.
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function decayKarma(\Components\Cron\Models\Job $job)
	{

		foreach (Scale::all()->whereEquals('state', 1)->rows() as $scale)
		{
			if (!$scale->decays())
			{
				continue;
			}

			$perDay = abs((float) $scale->get('decay_per_day'));
			$toward = (float) $scale->get('decay_toward');
			$idle   = (int) $scale->get('decay_after_days');

			$cutoff = Date::of('now')->modify('-' . max($idle, 0) . ' days')->toSql();

			$balances = Balance::all()
				->whereEquals('scale_id', $scale->get('id'))
				->where('last_event', '<', $cutoff)
				->rows();

			foreach ($balances as $balance)
			{
				$current = (float) $balance->get('raw');

				if ($current == $toward)
				{
					continue;
				}

				// Move toward the target, never past it.
				$step = ($current > $toward)
					? -min($perDay, $current - $toward)
					:  min($perDay, $toward - $current);

				if (!$step)
				{
					continue;
				}

				Karma::adjust($balance->get('user_id'), $scale, $step, 'karma.decay', array(
					'source_type' => 'plg_cron_karma',
					'source_id'   => (int) $scale->get('id')
				));
			}
		}

		return true;
	}

	/**
	 * Retire ledger entries that have passed their expiry
	 *
	 * A time-limited award is reversed the same way any other is, so the
	 * balance follows and the history still reads correctly.
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function expireKarma(\Components\Cron\Models\Job $job)
	{
		$now = Date::of('now')->toSql();

		$expired = Ledger::all()
			->whereEquals('state', Ledger::STATE_ACTIVE)
			->where('expires', 'IS NOT', null)
			->where('expires', '<=', $now)
			->rows();

		foreach ($expired as $entry)
		{
			Karma::revoke(
				$entry->get('source_type'),
				$entry->get('source_id'),
				$entry->get('rule'),
				$entry->get('subject_id')
			);
		}

		return true;
	}

	/**
	 * Rebuild balances from the ledger
	 *
	 * The repair job, and the thing that makes "a balance is a cache of the
	 * ledger" true rather than aspirational. Needed after a rule's worth
	 * changes, and safe to run against a live hub: it compares before it
	 * writes and touches only rows that actually disagree.
	 *
	 * @param   object  $job  \Components\Cron\Models\Job
	 * @return  bool
	 */
	public function recalculateKarma(\Components\Cron\Models\Job $job)
	{
		$now     = Date::of('now')->toSql();
		$changed = 0;

		foreach (Scale::all()->rows() as $scale)
		{
			$subjects = Ledger::all()
				->select('subject_id')
				->whereEquals('scale_id', $scale->get('id'))
				->group('subject_id')
				->rows();

			foreach ($subjects as $subject)
			{
				if (Balance::rebuild($subject->get('subject_id'), $scale, $now))
				{
					$changed++;
				}
			}
		}

		if ($changed)
		{
			Log::debug('plg_cron_karma: rebuilt ' . $changed . ' balance(s)');
		}

		return true;
	}
}
