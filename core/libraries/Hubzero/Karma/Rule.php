<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma;

use Hubzero\Database\Relational;

/**
 * What a karma source is worth
 *
 * Rules are rows rather than constants so an administrator can retune the
 * economy without a deploy, and so a component can emit an event before
 * anybody has decided what it is worth.
 */
class Rule extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'karma';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'alias';

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
		'alias' => 'notempty',
		'title' => 'notempty'
	);

	/**
	 * Published state
	 */
	const STATE_UNPUBLISHED = 0;
	const STATE_PUBLISHED   = 1;

	/**
	 * Cache of rules already loaded by alias
	 *
	 * @var  array
	 */
	protected static $cache = array();

	/**
	 * Load an active rule by alias, optionally within one scale
	 *
	 * A rule alias is unique within a scale, so an award naming a scale gets
	 * that scale's rule, and an award naming none gets the first active rule
	 * with that alias.
	 *
	 * @param   string   $alias
	 * @param   integer  $scaleId
	 * @return  object
	 */
	public static function oneByAlias($alias, $scaleId = null)
	{
		$key = (string) $alias . ':' . (int) $scaleId;

		if (!isset(self::$cache[$key]))
		{
			$query = self::all()
				->whereEquals('alias', (string) $alias)
				->whereEquals('state', self::STATE_PUBLISHED);

			if ($scaleId)
			{
				$query->whereEquals('scale_id', (int) $scaleId);
			}

			self::$cache[$key] = $query->row();
		}

		return self::$cache[$key];
	}

	/**
	 * Forget any cached rules
	 *
	 * @return  void
	 */
	public static function forget()
	{
		self::$cache = array();
	}

	/**
	 * Defines a belongs to one relationship with a scale
	 *
	 * @return  object
	 */
	public function scale()
	{
		return $this->belongsToOne('Hubzero\Karma\Scale', 'scale_id');
	}

	/**
	 * Is this rule capped per day?
	 *
	 * @return  bool
	 */
	public function hasDailyCap()
	{
		return ((int) $this->get('daily_cap') > 0);
	}

	/**
	 * Is this rule capped per source?
	 *
	 * @return  bool
	 */
	public function hasSourceCap()
	{
		return ((int) $this->get('per_source_cap') > 0);
	}
}
