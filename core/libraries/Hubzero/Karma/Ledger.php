<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * One karma event
 *
 * The ledger is append-only: rows are inserted, reversed by flipping state,
 * and eventually pruned, but never edited. A balance is a cache of this
 * table, which is what makes it safe to rebuild.
 */
class Ledger extends Relational
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
		'scale_id'   => 'positive|nonzero',
		'subject_id' => 'positive|nonzero'
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
	 * Entry state
	 */
	const STATE_REVERSED = 0;
	const STATE_ACTIVE   = 1;

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
	 * Defines a belongs to one relationship between entry and subject
	 *
	 * @return  object
	 */
	public function subject()
	{
		return $this->belongsToOne('Hubzero\User\User', 'subject_id');
	}

	/**
	 * Defines a belongs to one relationship between entry and actor
	 *
	 * @return  object
	 */
	public function actor()
	{
		return $this->belongsToOne('Hubzero\User\User', 'actor_id');
	}

	/**
	 * Transform params into a Registry
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!is_object($this->params))
		{
			$this->params = new Registry($this->get('params'));
		}

		return $this->params;
	}

	/**
	 * Is this entry still counted?
	 *
	 * @return  bool
	 */
	public function isActive()
	{
		return ((int) $this->get('state') == self::STATE_ACTIVE);
	}

	/**
	 * Has this entry passed its expiry?
	 *
	 * @param   string  $now  SQL datetime to compare against
	 * @return  bool
	 */
	public function hasExpired($now = null)
	{
		$expires = $this->get('expires');

		if (!$expires || $expires == '0000-00-00 00:00:00')
		{
			return false;
		}

		$now = $now ?: \Date::of('now')->toSql();

		return ($expires <= $now);
	}
}
