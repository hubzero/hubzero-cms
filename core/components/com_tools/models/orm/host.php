<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

include_once __DIR__ . '/zone.php';

/**
 * Tool host model
 *
 * @uses \Hubzero\Database\Relational
 */
class Host extends Relational
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
	protected $table = 'host';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'hostname';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'hostname' => 'notempty',
		'status'   => 'notempty'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticHostname($data)
	{
		return preg_replace("/[^A-Za-z0-9-.]/", '', strtolower($data['hostname']));
	}

	/**
	 * Retrieves a one to one model relationship with zone
	 *
	 * @return  object
	 */
	public function zone()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Zone', 'zone_id');
	}

	/**
	 * Retrieves one row loaded by hostname field
	 *
	 * @param   string  $hostname
	 * @return  mixed
	 */
	public static function oneByHostname($hostname)
	{
		return self::blank()
			->whereEquals('hostname', $hostname)
			->row();
	}
}
