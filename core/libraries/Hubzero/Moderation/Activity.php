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
 * How much of a thing somebody has been reading
 *
 * Moderation is offered to people who read, not to people who post, and the
 * platform keeps no per-page read log to derive that from. This is the
 * smallest thing that answers the question: a daily count per member per item
 * type, pruned once it is too old to matter.
 *
 * Only signed-in members are recorded, and signed-in members are never
 * page-cached, so this write never sits on a cached path.
 */
class Activity extends Relational
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
	public $orderBy = 'day';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Note that somebody read something
	 *
	 * @param   integer  $userId
	 * @param   string   $itemType
	 * @param   string   $day       Y-m-d, defaulting to today
	 * @return  bool
	 */
	public static function record($userId, $itemType, $day = null)
	{
		$userId = (int) $userId;

		if (!$userId)
		{
			return false;
		}

		$day = $day ?: with(new Date('now'))->format('Y-m-d');

		$row = self::all()
			->whereEquals('user_id', $userId)
			->whereEquals('item_type', (string) $itemType)
			->whereEquals('day', $day)
			->row();

		if (!$row->get('id'))
		{
			$row->set(array(
				'user_id'   => $userId,
				'item_type' => (string) $itemType,
				'day'       => $day,
				'count'     => 0
			));
		}

		$row->set('count', (int) $row->get('count', 0) + 1);

		return (bool) $row->save();
	}

	/**
	 * How much one member has read lately
	 *
	 * @param   integer  $userId
	 * @param   string   $itemType
	 * @param   string   $since     Y-m-d
	 * @return  integer
	 */
	public static function countFor($userId, $itemType, $since)
	{
		$rows = self::all()
			->whereEquals('user_id', (int) $userId)
			->whereEquals('item_type', (string) $itemType)
			->where('day', '>=', $since)
			->rows();

		$total = 0;

		foreach ($rows as $row)
		{
			$total += (int) $row->get('count');
		}

		return $total;
	}

	/**
	 * Forget anything older than the window
	 *
	 * @param   string  $before  Y-m-d
	 * @return  integer  rows removed
	 */
	public static function prune($before)
	{
		$rows    = self::all()->where('day', '<', $before)->rows();
		$removed = 0;

		foreach ($rows as $row)
		{
			if ($row->destroy())
			{
				$removed++;
			}
		}

		return $removed;
	}
}
