<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Database\Relational;
use Hubzero\Utility\Date;

/**
 * What one member may currently spend, on one kind of item
 *
 * Credits are per item type, so a hub can run crowd moderation on its forum
 * without that capacity leaking into anything else it later registers.
 */
class Wallet extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'moderation';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'credits';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'user_id'   => 'positive|nonzero',
		'item_type' => 'notempty'
	);

	/**
	 * Load a wallet, or a blank one ready to be saved
	 *
	 * @param   integer  $userId
	 * @param   string   $itemType
	 * @return  object
	 */
	public static function oneOrNew($userId, $itemType = null)
	{
		// Relational::oneOrNew takes an id; this overload takes the pair the
		// table is actually keyed on, which is what every caller here has.
		if (is_null($itemType))
		{
			return parent::oneOrNew($userId);
		}

		$wallet = self::all()
			->whereEquals('user_id', (int) $userId)
			->whereEquals('item_type', (string) $itemType)
			->row();

		if (!$wallet->get('id'))
		{
			$wallet->set(array(
				'user_id'   => (int) $userId,
				'item_type' => (string) $itemType,
				'credits'   => 0
			));
		}

		return $wallet;
	}

	/**
	 * How many credits are spendable right now
	 *
	 * Credits past their expiry are not spendable even before the cron job
	 * gets to them, so a wallet never lies between passes.
	 *
	 * @param   string  $now  SQL datetime to compare against
	 * @return  integer
	 */
	public function spendable($now = null)
	{
		if ($this->hasExpired($now))
		{
			return 0;
		}

		return max(0, (int) $this->get('credits'));
	}

	/**
	 * Have this wallet's credits passed their expiry?
	 *
	 * @param   string  $now
	 * @return  bool
	 */
	public function hasExpired($now = null)
	{
		$expires = $this->get('credits_expire');

		if (!$expires || $expires == '0000-00-00 00:00:00')
		{
			return false;
		}

		$now = $now ?: with(new Date('now'))->toSql();

		return ($expires <= $now);
	}

	/**
	 * Is this member willing to moderate?
	 *
	 * @return  bool
	 */
	public function isWilling()
	{
		return (bool) $this->get('willing', 1);
	}
}
