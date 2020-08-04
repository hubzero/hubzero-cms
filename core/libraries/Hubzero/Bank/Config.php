<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Bank;

use Hubzero\Database\Relational;
use Hubzero\Base\Obj;

/**
 * Class for getting and setting user point configuration
 */
class Config extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'users_points';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__users_points_config';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Constructor
	 * Loads points configuration
	 *
	 * @return  object
	 */
	public static function values()
	{
		$pc = self::all()->rows();

		$config = new Obj;

		foreach ($pc as $p)
		{
			$config->set($p->get('alias'), $p->get('points'));
		}

		return $config;
	}
}
