<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Utility\Date;

/**
 * The passes that keep credits moving
 *
 * Granting and expiry are two halves of one idea: credits are a licence to
 * act soon, not a balance to accumulate. Somebody who was handed five and
 * spent none has told you they did not want them, and holding them forever
 * would slowly drain the pool into the hands of people not using it.
 */
class Economy
{
	/**
	 * Which item type this is the economy for
	 *
	 * @var  string
	 */
	protected $itemType = '';

	/**
	 * How credits are handed out
	 *
	 * @var  object
	 */
	protected $grantor = null;

	/**
	 * Who may receive them
	 *
	 * @var  object
	 */
	protected $eligibility = null;

	/**
	 * Constructor
	 *
	 * @param   string  $itemType
	 * @param   object  $grantor
	 * @param   object  $eligibility
	 * @return  void
	 */
	public function __construct($itemType, Grantor $grantor = null, Eligibility $eligibility = null)
	{
		$this->itemType    = (string) $itemType;
		$this->grantor     = $grantor ?: new Grantor\IntervalGrantor();
		$this->eligibility = $eligibility ?: new Eligibility();
	}

	/**
	 * Hand out credits, and record what happened
	 *
	 * @return  object  the Grant row
	 */
	public function grant()
	{
		$pool   = $this->eligibility->pool($this->itemType);
		$counts = $this->grantor->run($this->itemType, $pool);

		$counts['credits_spent'] = $this->spentSinceLastPass();

		return Grant::record($this->itemType, $this->grantor->name(), $counts);
	}

	/**
	 * Take back credits nobody spent in time
	 *
	 * @return  integer  credits removed
	 */
	public function expire()
	{
		$now      = with(new Date('now'))->toSql();
		$expired  = 0;

		$wallets = Wallet::all()
			->whereEquals('item_type', $this->itemType)
			->where('credits', '>', 0)
			->where('credits_expire', 'IS NOT', null)
			->where('credits_expire', '<=', $now)
			->rows();

		foreach ($wallets as $wallet)
		{
			$lost = (int) $wallet->get('credits');

			$wallet->set(array(
				'credits'        => 0,
				'credits_expire' => null,
				'expired'        => (int) $wallet->get('expired', 0) + $lost
			));

			if ($wallet->save())
			{
				$expired += $lost;
			}
		}

		return $expired;
	}

	/**
	 * How many credits were spent since the previous pass
	 *
	 * Read from the log rather than counted as it happens, so that a missed
	 * pass does not lose the number.
	 *
	 * @return  integer
	 */
	protected function spentSinceLastPass()
	{
		$last = Grant::all()
			->whereEquals('item_type', $this->itemType)
			->order('created', 'desc')
			->row();

		$query = Log::all()->whereEquals('item_type', $this->itemType);

		if ($last->get('created'))
		{
			$query->where('created', '>', $last->get('created'));
		}

		$spent = 0;

		foreach ($query->rows() as $entry)
		{
			$spent += (int) $entry->get('credits_spent');
		}

		return $spent;
	}
}
