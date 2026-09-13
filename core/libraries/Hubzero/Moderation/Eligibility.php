<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Karma\Karma;
use Hubzero\Utility\Date;

/**
 * Who may be handed moderation credits
 *
 * Shared by every grantor, so that changing how credits are handed out never
 * quietly changes who can receive them. The order matters: the permission is
 * checked first because it is also the off switch. A hub that grants the
 * moderating permission to nobody has an empty pool, so nothing is issued and
 * moderation belongs entirely to whoever may moderate without spending.
 */
class Eligibility
{
	/**
	 * Defaults, overridable per call
	 *
	 * @var  array
	 */
	protected $config = array(
		'permission'           => '',
		'eligible_hitcount'    => 3,
		'activity_window_days' => 2,
		'min_account_age_days' => 30,
		'karma_scale'          => 'global',
		'min_karma'            => 0
	);

	/**
	 * A callable answering "may this user id do the thing?"
	 *
	 * Injected so the library does not reach for a facade, and so a test can
	 * answer without an ACL.
	 *
	 * @var  callable
	 */
	protected $authoriser = null;

	/**
	 * Constructor
	 *
	 * @param   array     $config
	 * @param   callable  $authoriser
	 * @return  void
	 */
	public function __construct(array $config = array(), $authoriser = null)
	{
		$this->config = array_merge($this->config, $config);
		$this->authoriser = $authoriser;
	}

	/**
	 * Everybody who could be handed credits for an item type
	 *
	 * Drawn from the people who have been reading it, because moderation is
	 * offered to readers. Somebody who has read nothing is not in the pool
	 * however much karma they have.
	 *
	 * @param   string  $itemType
	 * @return  array   user ids
	 */
	public function pool($itemType)
	{
		$since = with(new Date('-' . max(1, (int) $this->config['activity_window_days']) . ' days'))->format('Y-m-d');

		$rows = Activity::all()
			->whereEquals('item_type', (string) $itemType)
			->where('day', '>=', $since)
			->rows();

		$read = array();

		foreach ($rows as $row)
		{
			$id = (int) $row->get('user_id');
			$read[$id] = (isset($read[$id]) ? $read[$id] : 0) + (int) $row->get('count');
		}

		$eligible = array();

		foreach ($read as $userId => $count)
		{
			if ($count < (int) $this->config['eligible_hitcount'])
			{
				continue;
			}

			if ($this->permits($userId, $itemType))
			{
				$eligible[] = $userId;
			}
		}

		return $eligible;
	}

	/**
	 * May this member be handed credits?
	 *
	 * @param   integer  $userId
	 * @param   string   $itemType
	 * @return  bool
	 */
	public function permits($userId, $itemType)
	{
		$userId = (int) $userId;

		if (!$userId)
		{
			return false;
		}

		// The off switch, and deliberately first.
		if (!$this->authorised($userId))
		{
			return false;
		}

		$wallet = Wallet::oneOrNew($userId, $itemType);

		if (!$wallet->isWilling())
		{
			return false;
		}

		if ((int) $this->config['min_karma'] != 0 || $this->config['karma_scale'])
		{
			if (!$this->hasKarma($userId))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Does the hub let this member moderate at all?
	 *
	 * With no permission configured and no authoriser supplied, everybody
	 * passes: a library that refused by default would be impossible to adopt
	 * incrementally.
	 *
	 * @param   integer  $userId
	 * @return  bool
	 */
	protected function authorised($userId)
	{
		if (!$this->config['permission'])
		{
			return true;
		}

		if (is_callable($this->authoriser))
		{
			return (bool) call_user_func($this->authoriser, $userId, $this->config['permission']);
		}

		return true;
	}

	/**
	 * Is this member's standing high enough?
	 *
	 * @param   integer  $userId
	 * @return  bool
	 */
	protected function hasKarma($userId)
	{
		if (!class_exists('\\Hubzero\\Karma\\Karma'))
		{
			return true;
		}

		return Karma::atLeast($userId, (float) $this->config['min_karma'], $this->config['karma_scale']);
	}
}
