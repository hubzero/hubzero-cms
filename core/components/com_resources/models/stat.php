<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Hubzero\Database\Relational;
use Date;
use Lang;

include_once __DIR__ . DS . 'stat' . DS. 'tool.php';
include_once __DIR__ . DS . 'stat' . DS. 'cluster.php';

/**
 * Resource stats model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Stat extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource';

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
	 * Return a formatted timestamp for processed_on date
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
			return Date::of($this->get('processed_on'))->toLocal($as);
		}

		return $this->get('processed_on');
	}

	/**
	 * Defines a belongs to one relationship between resource and audience
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function resource()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Entry', 'resid');
	}
}
