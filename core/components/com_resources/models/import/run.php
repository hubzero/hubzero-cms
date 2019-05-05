<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Import;

use Hubzero\Database\Relational;
use Date;
use User;
use Lang;

/**
 * Resource import run model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Run extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource_import';

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
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'import_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ran_at',
		'ran_by'
	);

	/**
	 * Generates automatic ran at field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 **/
	public function automaticRanAt($data)
	{
		return (isset($data['ran_at']) && $data['ran_at'] ? $data['ran_at'] : Date::toSql());
	}

	/**
	 * Generates automatic ran by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 **/
	public function automaticRanBy($data)
	{
		return (isset($data['ran_by']) && $data['ran_by'] ? (int)$data['ran_by'] : (int)User::get('id'));
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function ranAt($as='')
	{
		if (strtolower($as) == 'date')
		{
			$as = Lang::txt('DATE_FORMAT_HZ1');
		}

		if (strtolower($as) == 'time')
		{
			$as = Lang::txt('TIME_FORMAT_HZ1');
		}

		if ($as)
		{
			return Date::of($this->get('ran_at'))->toLocal($as);
		}

		return $this->get('ran_at');
	}

	/**
	 * Defines a belongs to one relationship between audience and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function ranBy()
	{
		return $this->belongsToOne('Hubzero\User\User', 'ran_by');
	}

	/**
	 * Defines a belongs to one relationship between resource and audience
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function import()
	{
		return $this->belongsToOne('Components\Resources\Models\Import', 'import_id');
	}

	/**
	 * Add to the processed number on this run
	 *
	 * @param   integer  $number  Number to increpemnt by
	 * @return  void
	 */
	public function processed($number = 1)
	{
		$this->set('processed', $this->get('processed') + $number);
		$this->save();
	}
}
