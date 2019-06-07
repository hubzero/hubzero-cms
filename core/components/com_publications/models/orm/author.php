<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Model class for publication version author
 */
class Author extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'publication';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'publication_version_id' => 'positive|nonzero'
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
	 * Establish relationship to parent version
	 *
	 * @return  object
	 */
	public function version()
	{
		return $this->belongsToOne('Version');
	}

	/**
	 * Get last order
	 *
	 * @return  integer
	 */
	public function getLastOrder()
	{
		return self::all()
			->whereEquals('publication_version_id', $this->get('publication_version_id'))
			->order('ordering', 'desc')
			->row()
			->get('ordering', 0);
	}
}
