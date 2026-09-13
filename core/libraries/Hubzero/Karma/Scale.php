<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma;

use Hubzero\Database\Relational;
use Hubzero\Utility\Str;

/**
 * A named dimension of reputation
 *
 * A scale owns the bounds a balance is clamped to, the decay applied to it,
 * the adjective bands it is described with, and who may see it.
 */
class Scale extends Relational
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
		'alias' => 'notempty',
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias'
	);

	/**
	 * Visibility to the subject themselves
	 */
	const SELF_EXACT     = 'exact';
	const SELF_ADJECTIVE = 'adjective';

	/**
	 * Visibility to everybody else
	 */
	const PUBLIC_EXACT     = 'exact';
	const PUBLIC_ADJECTIVE = 'adjective';
	const PUBLIC_OPT_IN    = 'opt_in';
	const PUBLIC_HIDDEN    = 'hidden';

	/**
	 * Cache of scales already loaded by alias
	 *
	 * Award and read paths hit the same handful of scales repeatedly within a
	 * request, and they are effectively static configuration.
	 *
	 * @var  array
	 */
	protected static $cache = array();

	/**
	 * Generates automatic alias value
	 *
	 * Dots are kept: aliases are namespaced, as in story.comment.
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && trim($data['alias']) != '') ? $data['alias'] : (isset($data['title']) ? $data['title'] : '');
		$alias = str_replace(' ', '-', strtolower(trim($alias)));
		$alias = preg_replace('/[^a-z0-9\-\.]/', '', $alias);

		return $alias;
	}

	/**
	 * Load a scale by its alias, from the per-request cache where possible
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
	 * Forget any cached scales
	 *
	 * @return  void
	 */
	public static function forget()
	{
		self::$cache = array();
	}

	/**
	 * Constrain a value to this scale's bounds
	 *
	 * @param   float  $value
	 * @return  float
	 */
	public function clamp($value)
	{
		$value = (float) $value;

		$floor   = (float) $this->get('floor');
		$ceiling = (float) $this->get('ceiling');

		if ($value < $floor)
		{
			$value = $floor;
		}

		if ($value > $ceiling)
		{
			$value = $ceiling;
		}

		return $value;
	}

	/**
	 * Describe a value with this scale's adjective bands
	 *
	 * @param   float  $value
	 * @return  string
	 */
	public function adjective($value)
	{
		return Bands::lookup($this->get('adjectives'), $value, (string) $value);
	}

	/**
	 * Does this scale decay?
	 *
	 * @return  bool
	 */
	public function decays()
	{
		return ((float) $this->get('decay_per_day') != 0.0);
	}

	/**
	 * How many ledger entries name this scale
	 *
	 * Used to refuse a deletion that would silently discard everybody's
	 * standing on the scale.
	 *
	 * @return  integer
	 */
	public function ledgerCount()
	{
		if (!$this->get('id'))
		{
			return 0;
		}

		return Ledger::all()
			->whereEquals('scale_id', (int) $this->get('id'))
			->total();
	}

	/**
	 * Defines a one to many relationship with ledger entries
	 *
	 * @return  object
	 */
	public function entries()
	{
		return $this->oneToMany('Hubzero\Karma\Ledger', 'scale_id');
	}

	/**
	 * Defines a one to many relationship with rules
	 *
	 * @return  object
	 */
	public function rules()
	{
		return $this->oneToMany('Hubzero\Karma\Rule', 'scale_id');
	}

	/**
	 * Defines a one to many relationship with balances
	 *
	 * @return  object
	 */
	public function balances()
	{
		return $this->oneToMany('Hubzero\Karma\Balance', 'scale_id');
	}
}
