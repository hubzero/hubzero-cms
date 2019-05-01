<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'block.php';

/**
 * Model class for publication type
 */
class Type extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'publication_master';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'type'  => 'notempty',
		'alias' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
	);

	/**
	 * Get last order
	 *
	 * @return  integer
	 */
	public function getLastOrder()
	{
		return self::all()
			->order('ordering', 'desc')
			->row()
			->get('ordering', 0);
	}

	/**
	 * Check usage
	 *
	 * @return  integer
	 */
	public function checkUsage()
	{
		require_once __DIR__ . DS . 'publication.php';

		return Publication::all()
			->whereEquals('master_type', $this->get('id'))
			->total();
	}

	/**
	 * Find one record by ordering
	 *
	 * @param   integer  $ordering
	 * @return  object
	 */
	public static function oneByOrdering($ordering)
	{
		return self::all()
			->whereEquals('ordering', (int)$ordering)
			->row();
	}
}
