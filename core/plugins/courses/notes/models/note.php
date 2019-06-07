<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Notes\Models;

use Hubzero\Database\Relational;

/**
 * Model class for course notes
 */
class Note extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'courses_member';

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
		'scope'      => 'notempty',
		'scope_id'   => 'positive|nonzero',
		'section_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'state'
	);

	/**
	 * Generates automatic state field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticState()
	{
		$data['state'] = (isset($data['state']) ? $data['state'] : 1);
		return $data['state'];
	}

	/**
	 * Get the parent user associated with this entry
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}
}
