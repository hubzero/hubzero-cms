<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Windowstools\Models;

use Hubzero\Database\Relational;
use User;
use Date;

/**
 * Resource page model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Page extends Relational
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
	public $orderBy = 'ordering';

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
		'title' => 'notempty',
		'content' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'title',
		'alias',
		'modified',
		'modified_by'
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
	public function automaticTitle($data)
	{
		$title = strip_tags($data['title']);

		return trim($title);
	}

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
		$alias = str_replace(' ', '-', $alias);

		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic created field value
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticModified($data)
	{
		if (!isset($data['modified']) || !$data['modified'])
		{
			$data['modified'] = Date::toSql();
		}
		return $data['modified'];
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @param   array  $data
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		if (!isset($data['modified_by']))
		{
			$data['modified_by'] = User::get('id');
		}
		return $data['modified_by'];
	}
}
