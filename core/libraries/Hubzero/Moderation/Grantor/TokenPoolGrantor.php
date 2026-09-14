<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation\Grantor;

use Hubzero\Moderation\Grantor;
use Hubzero\Moderation\Wallet;
use Hubzero\Moderation\Activity;
use Hubzero\Utility\Date;
use Hubzero\Database\Query;

/**
 * Moderation capacity tied to how busy the site actually is
 *
 * The interval grantor hands out a fixed share of the eligible pool on a
 * timer, which means an administrator is guessing at how much moderation the
 * site needs. This one works the other way round: conversation mints tokens,
 * tokens accumulate against the people who read, and when somebody has
 * enough they become a moderator. Nobody sets a rate — the rate follows the
 * traffic.
 *
 * That is the better mechanism on a busy site and it is useless on a quiet
 * one, where the mint never reaches the price of a single grant and the whole
 * thing silently issues nothing. Which is why the interval grantor is the
 * default and this is the one a hub switches to once the grants record shows
 * it is ready.
 *
 * Tokens are internal. They are not a second currency, they are never shown to
 * a member, and nothing outside this class reads the column.
 */
class TokenPoolGrantor implements Grantor
{
	/**
	 * Settings in force
	 *
	 * The mint follows readership, because reading a discussion is the signal
	 * the platform actually records — there is no per-comment hook here, and a
	 * grantor that reached into com_story to count comments would stop being
	 * generic over item types.
	 *
	 * That choice sets the scale. Reads outnumber comments by roughly an order
	 * of magnitude, so the rate per read has to be correspondingly small: one
	 * token a read, against a grant priced at tokens_per_credit ×
	 * credits_per_grant — 8 × 5, so 40. A hub seeing two hundred discussion
	 * reads a day mints two hundred tokens, which is five grants. On a hub
	 * quiet enough that this never reaches 40, nothing is issued at all, and
	 * that is the failure this grantor is known for rather than a bug.
	 *
	 * Watch the grants record rather than trusting these numbers. They follow
	 * from one assumption about traffic, and the assumption is the part most
	 * likely to be wrong.
	 *
	 * @var  array
	 */
	protected $config = array(
		'tokens_per_read'       => 1,
		'tokens_per_credit'     => 8,
		'max_tokens_add'        => 3,
		'expire_token_cost'     => 2,
		'credits_per_grant'     => 5,
		'credit_lifetime_hours' => 96
	);

	/**
	 * Constructor
	 *
	 * @param   array  $config
	 * @return  void
	 */
	public function __construct(array $config = array())
	{
		foreach ($this->config as $key => $value)
		{
			if (isset($config[$key]) && $config[$key] !== '')
			{
				$this->config[$key] = $config[$key];
			}
		}
	}

	/**
	 * What this grantor is called
	 *
	 * @return  string
	 */
	public function name()
	{
		return 'tokenpool';
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

		$minted    = $this->mint($itemType);
		$recycled  = $this->recycle($itemType);
		$available = $minted + $recycled;

		if ($available <= 0)
		{
			$counts['note'] = 'nothing minted this pass';

			return $counts;
		}

		$scattered = $this->scatter($itemType, $eligible, $available);

		if (!$scattered)
		{
			$counts['note'] = 'nobody to scatter to';

			return $counts;
		}

		$price = max(1, (int) $this->config['tokens_per_credit'] * (int) $this->config['credits_per_grant']);

		$counts = array_merge($counts, $this->convert($itemType, $eligible, $price));

		if (!$counts['granted'])
		{
			$counts['note'] = 'nobody has reached the price of a grant yet';
		}

		Query::purgeCache();

		return $counts;
	}

	/**
	 * Tokens minted by readership since the last pass
	 *
	 * Read from the activity table rather than counted from comments, so the
	 * grantor stays ignorant of what an item type actually is.
	 *
	 * @param   string  $itemType
	 * @return  integer
	 */
	protected function mint($itemType)
	{
		$since = Date::of('now')->subtract('1 day')->format('Y-m-d');

		// Through the model rather than raw SQL: it uses whatever connection
		// the rest of the library is on, which raw SQL through the container
		// does not, and it keeps this readable.
		$rows = Activity::all()
			->whereEquals('item_type', (string) $itemType)
			->where('day', '>=', $since)
			->rows();

		$reads = 0;

		foreach ($rows as $row)
		{
			$reads += (int) $row->get('count');
		}

		return $reads * max(0, (int) $this->config['tokens_per_read']);
	}

	/**
	 * Tokens recovered from credits nobody spent
	 *
	 * An expired credit is capacity the site paid for and did not use, so the
	 * tokens behind it come back rather than vanishing — less what the waste
	 * cost. Charging something matters: without it there is no difference
	 * between spending a credit and sitting on it.
	 *
	 * @param   string  $itemType
	 * @return  integer
	 */
	protected function recycle($itemType)
	{
		$now       = Date::of('now')->toSql();
		$recovered = 0;

		$stale = Wallet::all()
			->whereEquals('item_type', $itemType)
			->where('credits', '>', 0)
			->where('credits_expire', '!=', '')
			->where('credits_expire', '<', $now)
			->rows();

		$perCredit = max(0, (int) $this->config['tokens_per_credit'] - (int) $this->config['expire_token_cost']);

		foreach ($stale as $wallet)
		{
			$lost = (int) $wallet->get('credits');

			$wallet->set('credits', 0);
			$wallet->set('expired', (int) $wallet->get('expired') + $lost);

			if ($wallet->save())
			{
				$recovered += $lost * $perCredit;
			}
		}

		return $recovered;
	}

	/**
	 * Spread the available tokens over the people who are eligible
	 *
	 * Capped per person per pass, so one reader with an unusual appetite for
	 * the site cannot collect the whole mint and become the only moderator.
	 *
	 * @param   string   $itemType
	 * @param   array    $eligible
	 * @param   integer  $available
	 * @return  integer  how many tokens were actually placed
	 */
	protected function scatter($itemType, array $eligible, $available)
	{
		$ceiling = max(1, (int) $this->config['max_tokens_add']);
		$placed  = 0;

		shuffle($eligible);

		foreach ($eligible as $userId)
		{
			if ($available <= 0)
			{
				break;
			}

			$give = min($ceiling, $available);

			$wallet = Wallet::oneOrNew($userId, $itemType);
			$wallet->set('tokens', (int) $wallet->get('tokens') + $give);

			if ($wallet->save())
			{
				$available -= $give;
				$placed    += $give;
			}
		}

		return $placed;
	}

	/**
	 * Turn accumulated tokens into credits
	 *
	 * Whoever has saved up the price of a grant gets one, highest holdings
	 * first. Somebody still holding spendable credits is skipped — topping
	 * them up would let credits pool rather than circulate, which is the same
	 * rule the interval grantor follows.
	 *
	 * @param   string   $itemType
	 * @param   array    $eligible
	 * @param   integer  $price
	 * @return  array
	 */
	protected function convert($itemType, array $eligible, $price)
	{
		$counts  = array('granted' => 0, 'credits_issued' => 0);
		$amount  = max(1, (int) $this->config['credits_per_grant']);
		$now     = Date::of('now')->toSql();
		$expires = Date::of('now')->add('+' . max(1, (int) $this->config['credit_lifetime_hours']) . ' hours')->toSql();

		$holders = array();

		foreach ($eligible as $userId)
		{
			$wallet = Wallet::oneOrNew($userId, $itemType);

			if ((int) $wallet->get('tokens') < $price || $wallet->spendable() > 0)
			{
				continue;
			}

			$holders[] = $wallet;
		}

		usort($holders, function ($a, $b)
		{
			return (int) $b->get('tokens') - (int) $a->get('tokens');
		});

		foreach ($holders as $wallet)
		{
			$wallet->set(array(
				'tokens'         => (int) $wallet->get('tokens') - $price,
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
