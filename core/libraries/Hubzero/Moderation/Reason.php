<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Database\Relational;

/**
 * Why a moderator moved a score
 *
 * Reasons are rows, per item type, so a forum and a news discussion can be
 * moderated in different vocabularies — and so an administrator can retune
 * what each is worth without a deploy.
 */
class Reason extends Relational
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
	public $orderBy = 'ordering';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'item_type' => 'notempty',
		'alias'     => 'notempty',
		'title'     => 'notempty'
	);

	/**
	 * Published state
	 */
	const STATE_UNPUBLISHED = 0;
	const STATE_PUBLISHED   = 1;

	/**
	 * Reasons already loaded, by item type
	 *
	 * @var  array
	 */
	protected static $cache = array();

	/**
	 * Every active reason for an item type, in order
	 *
	 * @param   string  $itemType
	 * @return  array
	 */
	public static function forType($itemType)
	{
		$itemType = (string) $itemType;

		if (!isset(self::$cache[$itemType]))
		{
			$reasons = array();

			$rows = self::all()
				->whereEquals('item_type', $itemType)
				->whereEquals('state', self::STATE_PUBLISHED)
				->order('ordering', 'asc')
				->rows();

			foreach ($rows as $row)
			{
				$reasons[$row->get('alias')] = $row;
			}

			self::$cache[$itemType] = $reasons;
		}

		return self::$cache[$itemType];
	}

	/**
	 * One active reason, by item type and alias
	 *
	 * @param   string  $itemType
	 * @param   string  $alias
	 * @return  mixed   object|null
	 */
	public static function oneByTypeAlias($itemType, $alias)
	{
		$reasons = self::forType($itemType);

		return isset($reasons[$alias]) ? $reasons[$alias] : null;
	}

	/**
	 * Forget any cached reasons
	 *
	 * @return  void
	 */
	public static function forget()
	{
		self::$cache = array();
	}

	/**
	 * Does this reason raise a score?
	 *
	 * @return  bool
	 */
	public function isPositive()
	{
		return ((int) $this->get('value') > 0);
	}

	/**
	 * May a moderation given for this reason be reviewed?
	 *
	 * @return  bool
	 */
	public function isReviewable()
	{
		return (bool) $this->get('reviewable');
	}
}
