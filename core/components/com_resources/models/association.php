<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Hubzero\Database\Relational;

/**
 * Resource association model
 *
 * @uses \Hubzero\Database\Relational
 */
class Association extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource';

	/**
	 * The table name, non-standard naming 
	 *
	 * @var  string
	 */
	protected $table = '#__resource_assoc';

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
		'parent_id' => 'positive|nonzero',
		'child_id'  => 'positive|nonzero'
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
	 * Get a record by its relationship
	 *
	 * @param   integer  $parent_id
	 * @param   integer  $child_id
	 * @return  object
	 */
	public static function oneByRelationship($parent_id, $child_id)
	{
		$row = self::all()
			->whereEquals('parent_id', $parent_id)
			->whereEquals('child_id', $child_id)
			->order('ordering', 'asc')
			->row();

		return $row;
	}

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  integer
	 */
	public function automaticOrdering($data)
	{
		if (empty($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->whereEquals('parent_id', $data['parent_id'])
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}


	/**
	 * Get the parent this resource is attached to
	 *
	 * @return  object
	 */
	public function parent()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Entry', 'parent_id', 'id');
	}

	/**
	 * Get the resource this represents
	 *
	 * @return  object
	 */
	public function resource()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Entry', 'child_id', 'id');
	}

	/**
	 * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
	 * Negative numbers move the row up in the sequence and positive numbers move it down.
	 *
	 * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
	 * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the ordering values.
	 * @return  bool     True on success.
	 */
	public function move($delta, $where = '')
	{
		// If the change is none, do nothing.
		if (empty($delta))
		{
			return true;
		}

		// Select the primary key and ordering values from the table.
		$query = self::all()
			->whereEquals('parent_id', $this->get('parent_id'));

		// If the movement delta is negative move the row up.
		if ($delta < 0)
		{
			$query->where('ordering', '<', (int) $this->get('ordering'));
			$query->order('ordering', 'desc');
		}
		// If the movement delta is positive move the row down.
		elseif ($delta > 0)
		{
			$query->where('ordering', '>', (int) $this->get('ordering'));
			$query->order('ordering', 'asc');
		}

		// Add the custom WHERE clause if set.
		if ($where)
		{
			$query->whereRaw($where);
		}

		// Select the first row with the criteria.
		$row = $query->row();

		// If a row is found, move the item.
		if ($row->get('id'))
		{
			$prev = $this->get('ordering');

			// Update the ordering field for this instance to the row's ordering value.
			$this->set('ordering', (int) $row->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}

			// Update the ordering field for the row to this instance's ordering value.
			$row->set('ordering', (int) $prev);

			// Check for a database error.
			if (!$row->save())
			{
				$this->addError($row->getError());
				return false;
			}
		}
		else
		{
			// Update the ordering field for this instance.
			$this->set('ordering', (int) $this->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Recalculates ordering values to try and keep
	 * them clean, in sequence, and no duplicates
	 *
	 * @return  bool  True on success.
	 */
	public function reorder()
	{
		// Select the primary key and ordering values from the table.
		$rows = self::all()
			->whereEquals('parent_id', $this->get('parent_id'))
			->order('ordering', 'asc')
			->order('id', 'asc')
			->rows();

		$i = 1;
		foreach ($rows as $row)
		{
			if ($row->get('ordering') != $i)
			{
				$row->set('ordering', $i);

				if (!$row->save())
				{
					$this->addError($row->getError());
					return false;
				}
			}

			$i++;
		}

		return true;
	}
}
