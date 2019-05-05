<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

include_once __DIR__ . '/zone/location.php';

/**
 * Tool zone model
 *
 * @uses \Hubzero\Database\Relational
 */
class Zone extends Relational
{
	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'zone'   => 'notempty',
		'master' => 'notempty',
		'state'  => 'notempty'
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
	 * Generates automatic zone field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticZone($data)
	{
		return preg_replace("/[^A-Za-z0-9\-\_\.]/", '', strtolower($data['zone']));
	}

	/**
	 * Generates automatic title field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticTitle($data)
	{
		if (!isset($data['title']) || !$data['title'])
		{
			$data['title'] = $data['zone'];
		}
		return $data['title'];
	}

	/**
	 * Get a list of locations
	 *
	 * @return  object
	 */
	public function locations()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Zone\\Location', 'zone_id');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove comments
		foreach ($this->locations as $location)
		{
			if (!$location->destroy())
			{
				$this->addError($location->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}
}
