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

namespace Components\Careerplans\Models;

use Hubzero\Database\Relational;
use User;
use Date;

include_once __DIR__ . DS . 'fieldset.php';

/**
 * Careerplan answer model
 */
class Answer extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'careerplans';

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
		'field_id' => 'positive|nonzero',
		'careerplan_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ordering',
		'created',
		'created_by'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'modified',
		'modified_by'
	);

	/**
	 * Has external data been loaded?
	 *
	 * @var  bool
	 */
	private $valuesLoaded = false;

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return (isset($data['id']) && $data['id'] ? Date::of('now')->toSql() : null);
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		return (isset($data['id']) && $data['id'] ? User::get('id') : 0);
	}

	/**
	 * Get creator
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
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
	 * Get parent application
	 *
	 * @return  object
	 */
	public function application()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Careerplan', 'careerplan_id');
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
				->whereEquals('careerplan_id', $this->get('careerplan_id'))
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Gets an attribute by key
	 *
	 * This will not retrieve properties directly attached to the model,
	 * even if they are public - those should be accessed directly!
	 *
	 * Also, make sure to access properties in transformers using the get method.
	 * Otherwise you'll just get stuck in a loop!
	 *
	 * @param   string  $key      The attribute key to get
	 * @param   mixed   $default  The value to provide, should the key be non-existent
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		// Some answers are complex enough we need
		// to ask the field to retrieve all the data
		if ($key == 'value' && !$this->valuesLoaded)
		{
			if ($this->field->get('type') == 'goals')
			{
				$goals = Goal::all()
					->whereEquals('careerplan_id', $this->careerplan_id)
					->whereEquals('field_id', $this->field_id)
					->order('ordering', 'asc')
					->rows();

				foreach ($goals as $goal)
				{
					$strategies = $goal->strategies()
						->order('ordering', 'asc')
						->rows()
						->toArray();

					$goal->set('strategy', $strategies);
				}

				$val = $goals->toArray();

				$this->set('value', $val);
			}

			$this->valuesLoaded = true;
		}

		return parent::get($key, $default);
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		if ($this->field->get('type') == 'goals')
		{
			$goals = Goal::all()
				->whereEquals('careerplan_id', $this->get('careerplan_id'))
				->rows();

			// Remove comments
			foreach ($goals as $goal)
			{
				if (!$goal->destroy())
				{
					$this->addError($goal->getError());
					return false;
				}
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   integer  $field_id
	 * @param   integer  $careerplan_id
	 * @return  object
	 */
	public static function oneByFieldAndCareerplan($field_id, $careerplan_id)
	{
		return self::all()
			->whereEquals('field_id', $field_id)
			->whereEquals('careerplan_id', $careerplan_id)
			->row();
	}

	/**
	 * Helper method to collect multi-value fields
	 *
	 * @param   mixed
	 * @return  array
	 */
	public static function collect($data)
	{
		$arr = array();

		foreach ($data as $datum)
		{
			if (!isset($arr[$datum->get('field_id')]))
			{
				$arr[$datum->get('field_id')] = $datum->get('value');
			}
			else
			{
				$values = $arr[$datum->get('field_id')];
				if (!is_array($values))
				{
					$values = array($values);
				}
				$values[] = $datum->get('value');

				$arr[$datum->get('field_id')] = $values;
			}
		}

		return $arr;
	}
}
