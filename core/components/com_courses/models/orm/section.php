<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Model class for a course section
 */
class Section extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'courses_offering';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'created';

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
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 100)
		{
			$alias = substr($alias . ' ', 0, 100);
			$alias = substr($alias, 0, strrpos($alias, ' '));
		}
		$alias = str_replace(' ', '_', $alias);

		return preg_replace("/[^a-zA-Z0-9_\-\.]/", '', strtolower($alias));
	}

	/**
	 * Get parent offering
	 *
	 * @return  object
	 */
	public function offering()
	{
		return $this->belongsToOne('Offering');
	}
}
