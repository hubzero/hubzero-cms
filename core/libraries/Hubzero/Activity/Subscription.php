<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Activity;

use Hubzero\Database\Relational;

/**
 * Activity subscriber
 */
class Subscription extends Relational
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
	public $orderBy = 'created';

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
		'user_id'  => 'positive|nonzero',
		'scope'    => 'notempty',
		'scope_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * Load a record by scope and scope ID
	 *
	 * @param   integer  $scope_id
	 * @param   string   $scope
	 * @param   integer  $user_id
	 * @return  object
	 */
	public static function oneByScope($scope_id, $scope, $user_id = 0)
	{
		$model = self::all()
			->whereEquals('scope_id', (int)$scope_id)
			->whereEquals('scope', (string)$scope);

		if ($user_id)
		{
			$model->whereEquals('user_id', (int)$user_id);
		}

		return $model->row();
	}
}
