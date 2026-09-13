<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation\Grantor;

use Hubzero\Moderation\Grantor;
use Hubzero\Moderation\Wallet;
use Hubzero\Utility\Date;

/**
 * A share of the eligible read it, every so often
 *
 * Four numbers and no hidden state: give `credits_per_grant` credits to
 * `grant_fraction` of the eligible pool, no oftener than every
 * `grant_interval_hours` per person, expiring after `credit_lifetime_hours`.
 *
 * It is deliberately dull. It produces moderators on a site with five
 * comments a day, which the alternative does not, and an administrator can
 * predict what it will do from the four numbers alone.
 */
class IntervalGrantor implements Grantor
{
	/**
	 * Settings
	 *
	 * @var  array
	 */
	protected $config = array(
		'credits_per_grant'     => 5,
		'credit_lifetime_hours' => 96,
		'grant_interval_hours'  => 72,
		'grant_fraction'        => 0.15
	);

	/**
	 * Constructor
	 *
	 * @param   array  $config
	 * @return  void
	 */
	public function __construct(array $config = array())
	{
		$this->config = array_merge($this->config, $config);
	}

	/**
	 * Name
	 *
	 * @return  string
	 */
	public function name()
	{
		return 'interval';
	}

	/**
	 * Hand out credits
	 *
	 * @param   string  $itemType
	 * @param   array   $eligible
	 * @return  array
	 */
	public function run($itemType, array $eligible)
	{
		$counts = array(
			'eligible'       => count($eligible),
			'granted'        => 0,
			'credits_issued' => 0
		);

		if (!$eligible)
		{
			return $counts;
		}

		$cutoff  = with(new Date('-' . max(0, (int) $this->config['grant_interval_hours']) . ' hours'))->toSql();
		$waiting = array();

		foreach ($eligible as $userId)
		{
			$wallet = Wallet::oneOrNew($userId, $itemType);

			// Somebody still holding spendable credits is not waiting for
			// more; topping them up would let credits pool rather than
			// circulate.
			if ($wallet->spendable() > 0)
			{
				continue;
			}

			$last = $wallet->get('last_granted');

			if ($last && $last > $cutoff)
			{
				continue;
			}

			$waiting[] = $wallet;
		}

		if (!$waiting)
		{
			$counts['note'] = 'nobody was due';
			return $counts;
		}

		shuffle($waiting);

		$take    = (int) ceil(count($waiting) * (float) $this->config['grant_fraction']);
		$take    = max(1, min($take, count($waiting)));
		$now     = with(new Date('now'))->toSql();
		$expires = with(new Date('+' . max(1, (int) $this->config['credit_lifetime_hours']) . ' hours'))->toSql();
		$amount  = max(1, (int) $this->config['credits_per_grant']);

		foreach (array_slice($waiting, 0, $take) as $wallet)
		{
			$wallet->set(array(
				'credits'        => $amount,
				'credits_expire' => $expires,
				'last_granted'   => $now
			));

			if ($wallet->save())
			{
				$counts['granted']++;
				$counts['credits_issued'] += $amount;
			}
		}

		return $counts;
	}
}
