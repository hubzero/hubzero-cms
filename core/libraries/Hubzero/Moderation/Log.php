<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Database\Relational;

/**
 * One act of moderation
 *
 * Written whether or not the score actually moved: a moderation that ran into
 * the item's bounds still happened, still cost a credit, and still deserves to
 * be reviewable. Those are recorded with active = 0.
 */
class Log extends Relational
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
	public $orderBy = 'created';

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
		'item_type' => 'notempty',
		'item_id'   => 'positive|nonzero',
		'user_id'   => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * Whether the moderation moved the score
	 */
	const INERT  = 0;
	const ACTIVE = 1;

	/**
	 * Review state
	 */
	const REVIEW_PENDING  = 0;
	const REVIEW_RESOLVED = 2;

	/**
	 * Has this member already moderated this item?
	 *
	 * @param   string   $itemType
	 * @param   integer  $itemId
	 * @param   integer  $userId
	 * @return  bool
	 */
	public static function exists($itemType, $itemId, $userId)
	{
		return (bool) self::all()
			->whereEquals('item_type', (string) $itemType)
			->whereEquals('item_id', (int) $itemId)
			->whereEquals('user_id', (int) $userId)
			->total();
	}

	/**
	 * Everything one member did inside one container
	 *
	 * @param   string   $itemType
	 * @param   integer  $containerId
	 * @param   integer  $userId
	 * @return  object
	 */
	public static function inContainer($itemType, $containerId, $userId)
	{
		return self::all()
			->whereEquals('item_type', (string) $itemType)
			->whereEquals('container_id', (int) $containerId)
			->whereEquals('user_id', (int) $userId)
			->rows();
	}

	/**
	 * Did this moderation move the score?
	 *
	 * @return  bool
	 */
	public function isActive()
	{
		return ((int) $this->get('active') === self::ACTIVE);
	}

	/**
	 * Defines a belongs to one relationship with a reason
	 *
	 * @return  object
	 */
	public function reason()
	{
		return $this->belongsToOne('Hubzero\Moderation\Reason', 'reason_id');
	}
}
