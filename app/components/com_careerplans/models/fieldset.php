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
use Date;
use User;

include_once __DIR__ . DS . 'field.php';

/**
 * Careerplan fieldset model
 */
class Fieldset extends Relational
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
		'label' => 'notempty',
		'name'  => 'notempty'
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
		'name',
		'modified',
		'modified_by'
	);

	/**
	 * Generates automatic name field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticName($data)
	{
		$name = (isset($data['name']) && $data['name'] ? $data['name'] : $data['label']);
		$name = strip_tags($name);
		$name = trim($name);

		return preg_replace("/[^a-zA-Z0-9_]/", '', strtolower($name));
	}

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return (isset($data['id']) && $data['id'] ? Date::of('now')->toSql() : '0000-00-00 00:00:00');
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
	 * Get field options
	 *
	 * @return  object
	 */
	public function fields()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Field', 'fieldset_id');
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
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = (int)$last->get('ordering') + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if ($this->isNew())
		{
			return true;
		}

		// Remove comments
		foreach ($this->fields as $field)
		{
			if (!$field->destroy())
			{
				$this->addError($field->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Generate a link to this record
	 *
	 * @return  string
	 */
	public function link()
	{
		return 'index.php?option=com_careerplans' . ($this->get('ordering') ? '&page=' . $this->get('ordering') : '');
	}

	/**
	 * Load a record by ordering value
	 *
	 * @param   integer  $ordering
	 * @return  object
	 */
	public static function oneByOrdering($ordering)
	{
		$row = self::all()
			->whereEquals('ordering', $ordering)
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
