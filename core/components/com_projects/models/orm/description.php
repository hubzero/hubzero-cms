<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Project description model
 */
class Description extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'project';

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
		'description_key' => 'notempty',
		'project_id'      => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ordering'
	);

	/**
	 * Get parent project
	 *
	 * @return  object
	 */
	public function project()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Project', 'project_id');
	}

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrdering($data)
	{
		if (!isset($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->whereEquals('project_id', $this->get('project_id'))
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Load a record by $description_key and $project_id combination
	 *
	 * @param   string   $description_key
	 * @param   integer  $project_id
	 * @return  object
	 */
	public static function oneByKeyAndProject($description_key, $project_id)
	{
		return self::all()
			->whereEquals('description_key', $description_key)
			->whereEquals('project_id', $project_id)
			->row();
	}

	/**
	 * Helper method to collect multi-value fields
	 *
	 * @param   array  $data
	 * @return  array
	 */
	public static function collect($data)
	{
		$arr = array();

		foreach ($data as $description)
		{
			if (!isset($arr[$description->get('description_key')]))
			{
				$arr[$description->get('description_key')] = $description->get('description_value');
			}
			else
			{
				$values = $arr[$description->get('description_key')];
				if (!is_array($values))
				{
					$values = array($values);
				}
				$values[] = $description->get('description_value');

				$arr[$description->get('description_key')] = $values;
			}
		}

		return $arr;
	}
}
