<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm\Description;

use Hubzero\Database\Relational;

/**
 * User profile field option model
 */
class Option extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'project_description';

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
		'label'    => 'notempty',
		'field_id' => 'positive|nonzero'
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
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'value',
		'checked'
	);

	/**
	 * Generates automatic value field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticValue($data)
	{
		if (!isset($data['value']) || !$data['value'])
		{
			$data['value'] = $data['label'];
		}

		return $data['value'];
	}

	/**
	 * Generates automatic checked field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticChecked($data)
	{
		if (!isset($data['checked']))
		{
			$data['checked'] = 0;
		}

		return (int)$data['checked'];
	}

	/**
	 * Get parent field
	 *
	 * @return  object
	 */
	public function field()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Field', 'field_id');
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
				->whereEquals('user_id', $this->get('user_id'))
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}
}
