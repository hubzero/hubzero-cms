<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma;

use Hubzero\Database\Relational;

/**
 * A banded threshold, the generalisation of Slashdot's comments_perday_bykarma
 *
 * A gate turns a karma value into whatever a caller needs — a rate limit, a
 * boolean, a label — without the caller knowing the bands.
 */
class Gate extends Relational
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
		'alias' => 'notempty'
	);

	/**
	 * Cache of gates already loaded by alias
	 *
	 * @var  array
	 */
	protected static $cache = array();

	/**
	 * Load a gate by alias
	 *
	 * @param   string  $alias
	 * @return  object
	 */
	public static function oneByAlias($alias)
	{
		$alias = (string) $alias;

		if (!isset(self::$cache[$alias]))
		{
			self::$cache[$alias] = self::all()
				->whereEquals('alias', $alias)
				->row();
		}

		return self::$cache[$alias];
	}

	/**
	 * Forget any cached gates
	 *
	 * @return  void
	 */
	public static function forget()
	{
		self::$cache = array();
	}

	/**
	 * Place a karma value in this gate's bands
	 *
	 * @param   float  $karma
	 * @return  mixed
	 */
	public function evaluate($karma)
	{
		return Bands::lookup($this->get('bands'), $karma, $this->get('default_value'));
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
}
