<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Carrerplan field option model
 */
class Option extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xgroups_description';

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
				->whereEquals('field_id', $this->get('field_id'))
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
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
	 * Load a record by ordering value
	 *
	 * @param   integer  $ordering
	 * @param   integer  $field_id
	 * @return  object
	 */
	public static function oneByOrdering($ordering, $field_id)
	{
		$row = self::all()
			->whereEquals('ordering', $ordering)
			->whereEquals('field_id', $field_id)
			->order('ordering', 'asc')
			->limit(1)
			->row();

		if (!$row)
		{
			$row = self::blank();
		}

		return $row;
	}

	/**
	 * Load a record by value
	 *
	 * @param   string   $value
	 * @param   integer  $field_id
	 * @return  object
	 */
	public static function oneByValue($value, $field_id)
	{
		$row = self::all()
			->whereEquals('value', $value)
			->whereEquals('field_id', $field_id)
			->order('ordering', 'asc')
			->limit(1)
			->row();

		if (!$row)
		{
			$row = self::blank();
		}

		return $row;
	}
}
