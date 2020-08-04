<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Activity;

use Hubzero\Database\Relational;

/**
 * Activity digest
 */
class Digest extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'activity';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'sent';

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
		'scope'    => 'notempty',
		'scope_id' => 'positive|nonzero'
	);

	/**
	 * Load a record by scope and scope ID
	 *
	 * @param   integer  $scope_id
	 * @param   string   $scope
	 * @return  object
	 */
	public static function oneByScope($scope_id, $scope)
	{
		return self::all()
			->whereEquals('scope_id', (int)$scope_id)
			->whereEquals('scope', (string)$scope)
			->row();
	}
}
