<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use Date;

/**
 * Projects activity model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Activity extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'project';

	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $table = '#__project_activity';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'id';

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
		'projectid' => 'positive|nonzero',
		'activity'  => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'recorded'
	);

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 * @since   2.0.0
	 **/
	public function automaticRecorded($data)
	{
		return (isset($data['recorded']) && $data['recorded'] ? $data['recorded'] : Date::toSql());
	}

	/**
	 * Get parent project
	 *
	 * @return  object
	 */
	public function project()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Project', 'projectid');
	}

	/**
	 * Get parent user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\\User\\User', 'userid');
	}
}
