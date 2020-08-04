<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Auditor;

use Hubzero\Database\Relational;

/**
 * Test result
 */
class Result extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'audit';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'processed';

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
		'scope_id' => 'positive|nonzero',
		'test_id'  => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $always = array(
		'processed'
	);

	/**
	 * Set processed timestamp
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticProcessed($data)
	{
		$dt = new \Hubzero\Utility\Date();
		return $dt->toSql();
	}

	/**
	 * Did the test pass?
	 *
	 * @return  bool
	 */
	public function passed()
	{
		return ($this->get('status') == 1);
	}

	/**
	 * Did the test fail?
	 *
	 * @return  bool
	 */
	public function failed()
	{
		return ($this->get('status') == -1);
	}

	/**
	 * Was the test skipped?
	 *
	 * @return  bool
	 */
	public function skipped()
	{
		return ($this->get('status') == 0);
	}

	/**
	 * Transform answer
	 *
	 * @return  string
	 */
	public function transformStatus()
	{
		if ($this->skipped())
		{
			return 'skipped';
		}

		if ($this->passed())
		{
			return 'passed';
		}

		if ($this->failed())
		{
			return 'failed';
		}

		return $this->get('status');
	}

	/**
	 * Load a record by scope and scope_id
	 *
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  object
	 */
	public static function oneByScope($scope, $scope_id)
	{
		return self::all()
			->whereEquals('scope', $scope)
			->whereEquals('scope_id', $scope_id)
			->ordered()
			->row();
	}
}
