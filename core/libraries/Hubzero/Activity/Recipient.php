<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Activity;

use Hubzero\Database\Relational;
use Hubzero\Utility\Date;

/**
 * Activity recipient
 */
class Recipient extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'activity';

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
		'log_id'   => 'positive|nonzero',
		'scope'    => 'notempty',
		'scope_id' => 'positive|nonzero'
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
	 * Defines a belongs to one relationship between recipient and log
	 *
	 * @return  object
	 */
	public function log()
	{
		return $this->belongsToOne('Hubzero\Activity\Log', 'log_id');
	}

	/**
	 * Check if entry has been viewed or not
	 *
	 * @return  boolean
	 */
	public function wasViewed()
	{
		if ($this->get('viewed')
		 && $this->get('viewed') != '0000-00-00 00:00:00')
		{
			return true;
		}

		return false;
	}

	/**
	 * Mark entry as having been viewed
	 *
	 * @return  boolean
	 */
	public function markAsViewed()
	{
		$dt = new Date('now');

		$this->set('viewed', $dt->toSql());

		return $this->save();
	}

	/**
	 * Mark entry as NOT having been viewed
	 *
	 * @return  boolean
	 */
	public function markAsNotViewed()
	{
		$this->set('viewed', null);

		return $this->save();
	}

	/**
	 * Mark entry as being published
	 *
	 * @return  boolean
	 */
	public function markAsPublished()
	{
		$this->set('state', self::STATE_PUBLISHED);

		return $this->save();
	}

	/**
	 * Mark entry as being unpublished
	 *
	 * @return  boolean
	 */
	public function markAsUnpublished()
	{
		$this->set('state', self::STATE_UNPUBLISHED);

		return $this->save();
	}

	/**
	 * Mark entry as starred
	 *
	 * @return  boolean
	 */
	public function markAsStarred()
	{
		$this->set('starred', 1);

		return $this->save();
	}

	/**
	 * Mark entry as not starred
	 *
	 * @return  boolean
	 */
	public function markAsNotStarred()
	{
		$this->set('starred', 0);

		return $this->save();
	}

	/**
	 * Modify query to only return published entries
	 *
	 * @return  object
	 */
	public function wherePublished()
	{
		$this->whereEquals($this->getTableName() . '.state', self::STATE_PUBLISHED);
		return $this;
	}

	/**
	 * Get all entries for a scope
	 *
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  boolean
	 */
	public static function allForScope($scope, $scope_id = 0)
	{
		if (!is_array($scope))
		{
			$scope = array($scope);
		}

		$recipient = self::all();

		$r = $recipient->getTableName();
		$l = \Hubzero\Activity\Log::blank()->getTableName();

		$recipient
			->select($r . '.*')
			->including('log')
			->join($l, $l . '.id', $r . '.log_id')
			->whereIn($r . '.scope', $scope)
			->whereEquals($r . '.scope_id', $scope_id);

		return $recipient;
	}
}
