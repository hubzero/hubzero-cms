<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm\Page;

use Hubzero\Database\Relational;
use Request;
use Date;

/**
 * Group page hit model
 */
class Hit extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'xgroups_pages';

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
		'gidNumber' => 'positive|nonzero',
		'pageid'    => 'positive|nonzero',
		'userid'    => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'date',
		'ip'
	);

	/**
	 * Generates automatic Date field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticDate($data)
	{
		return Date::of('now')->toSql();
	}

	/**
	 * Generates automatic IP field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticIp($data)
	{
		return Request::ip();
	}

	/**
	 * Get parent group
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->belongsToOne('Components\Groups\Models\Orm\Group', 'gidNumber');
	}

	/**
	 * Get parent page
	 *
	 * @return  object
	 */
	public function page()
	{
		return $this->belongsToOne('Components\Groups\Models\Orm\Page', 'pageid');
	}

	/**
	 * Defines a belongs to one relationship between hit and user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'userid');
	}
}
