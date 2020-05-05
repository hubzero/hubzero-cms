<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;
use Lang;

include_once __DIR__ . '/joblog.php';

/**
 * Tool job model
 *
 * @uses \Hubzero\Database\Relational
 */
class Job extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = 'job';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'jobid';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'sessnum' => 'positive|nonzero'
	);

	/**
	 * Defines a one to many relationship between location and zone
	 *
	 * @return  object
	 */
	public function logs()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Joblog', 'job');
	}
}
