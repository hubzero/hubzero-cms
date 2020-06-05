<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Stat;

use Hubzero\Database\Relational;
use Date;
use Lang;

/**
 * Resource stats clusters model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Cluster extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource_stats';

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
		'resid' => 'positive|nonzero'
	);

	/**
	 * Return a formatted timestamp for timestamp date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function processed($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			$as = Lang::txt('DATE_FORMAT_HZ1');
		}

		if ($as == 'time')
		{
			$as = Lang::txt('TIME_FORMAT_HZ1');
		}

		if ($as)
		{
			return Date::of($this->get('timestamp'))->toLocal($as);
		}

		return $this->get('timestamp');
	}

	/**
	 * Defines a belongs to one relationship between entry and resource
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function resource()
	{
		return $this->belongsToOne('Components\Resources\Models\Entry', 'resid');
	}

	/**
	 * Defines a belongs to one relationship between entry and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'uidNumber');
	}
}
