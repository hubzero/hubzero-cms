<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm\Zone;

use Hubzero\Database\Relational;
use Lang;

/**
 * Tool zone location model
 *
 * @uses \Hubzero\Database\Relational
 */
class Location extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'zone';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = 'zone_locations';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'zone_id' => 'positive|nonzero',
		'ipFROM'  => 'notempty'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('state', function($data)
		{
			$data['state'] = strtolower($data['state']);

			return (in_array($data['state'], array('up', 'down')) ? false : Lang::txt('Invalid state provided.'));
		});
	}

	/**
	 * Generates automatic ipFROM field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticIpFrom($data)
	{
		if (strpos($data['ipFROM'], '/') !== false)
		{
			$cidr         = explode('/', $data['ipFROM']);
			$data['ipFROM'] = ip2long($cidr[0]) & ((-1 << (32 - (int)$cidr[1])));
			$data['ipTO']   = ip2long($cidr[0]) + pow(2, (32 - (int)$cidr[1])) - 1;
		}

		if (strstr($data['ipFROM'], '.'))
		{
			$data['ipFROM'] = ip2long($data['ipFROM']);
		}

		return $data['ipFROM'];
	}

	/**
	 * Generates automatic ipTO field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticIpTo($data)
	{
		if (strpos($data['ipFROM'], '/') !== false)
		{
			$cidr         = explode('/', $data['ipFROM']);
			$data['ipFROM'] = ip2long($cidr[0]) & ((-1 << (32 - (int)$cidr[1])));
			$data['ipTO']   = ip2long($cidr[0]) + pow(2, (32 - (int)$cidr[1])) - 1;
		}

		if (strstr($data['ipTO'], '.'))
		{
			$data['ipTO'] = ip2long($data['ipTO']);
		}

		return $data['ipTO'];
	}

	/**
	 * Defines a belongs to one relationship between location and zone
	 *
	 * @return  object
	 */
	public function zone()
	{
		return $this->belongsToOne('Components\Tools\Models\Orm\Zone', 'zone_id');
	}
}
