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

namespace Components\Members\Models\Profile;

use Hubzero\Database\Relational;

include_once __DIR__ . DS . 'option.php';

/**
 * User profile field model
 */
class Field extends Relational
{
	/**
	 * Hidden state
	 *
	 * @var  integer
	 */
	const STATE_HIDDEN = 0;

	/**
	 * Optional state
	 *
	 * @var  integer
	 */
	const STATE_OPTIONAL = 1;

	/**
	 * Required state
	 *
	 * @var  integer
	 */
	const STATE_REQUIRED = 2;

	/**
	 * Read only state
	 *
	 * @var  integer
	 */
	const STATE_READONLY = 4;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'user_profile';

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
		'type'  => 'notempty'
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
		'name'
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
	 * Get field options
	 *
	 * @return  object
	 */
	public function options()
	{
		return $this->oneToMany('Option', 'field_id');
	}

	/**
	 * Get child fields
	 *
	 * @return  object
	 */
	public function children()
	{
		return $this->oneToMany('Field', 'parent');
	}

	/**
	 * Get parent field
	 *
	 * @return  object
	 */
	public function parent()
	{
		return $this->belongsToOne('Field', 'parent');
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
				->whereEquals('parent', $this->get('parent'))
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
		foreach ($this->options as $option)
		{
			if (!$option->destroy())
			{
				$this->addError($option->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Return if a field is required, option, read only, or hidden
	 *
	 * @param   string  $name     Property name
	 * @param   string  $default  Default property value
	 * @param   string  $task     Task to look up value for
	 * @return  string
	 */
	public static function state($name, $default = 'OOOO', $task = 'create')
	{
		switch ($task)
		{
			case 'register':
			case 'create': $index = 0; break;
			case 'proxy':  $index = 1; break;
			case 'update': $index = 2; break;
			case 'edit':   $index = 3; break;
			default:       $index = 0; break;
		}

		$configured = Component::params('com_members')->get($name);

		$default = str_pad($default, 4, '-');

		if (empty($configured))
		{
			$configured = $default;
		}

		$length = strlen($configured);

		if ($length <= $index)
		{
			$configured = $default;
		}

		$key = substr($configured, $index, 1);

		switch ($key)
		{
			case 'R': $val = self::STATE_REQUIRED; break;
			case 'O': $val = self::STATE_OPTIONAL; break;
			case 'U': $val = self::STATE_READONLY; break;
			case 'H':
			case '-':
			default : $val = self::STATE_HIDDEN; break;
		}

		return $val;
	}

	/**
	 * Helper method to force dependent fields to be skipped
	 * during validation when not chosen
	 *
	 * @param   mixed   $fields  Iterable object or array
	 * @param   array   $data
	 * @return  mixed
	 */
	public static function resolveDependencies($fields, $data = array())
	{
		if (empty($data))
		{
			return $fields;
		}

		$skip = array();

		foreach ($fields as $field)
		{
			foreach ($field->options as $option)
			{
				$selected = false;

				if (!$option->get('dependents'))
				{
					continue;
				}

				$events = json_decode($option->get('dependents', '[]'));

				if (empty($events))
				{
					continue;
				}

				if (isset($data[$field->get('name')]))
				{
					$values = $data[$field->get('name')];

					if (is_array($values) && in_array($option->get('value'), $values))
					{
						$selected = true;
					}
					else if ($values == $option->get('value'))
					{
						$selected = true;
					}
				}

				// If the option was chosen...
				// pass its dependents through validation
				if ($selected)
				{
					continue;
				}

				// If the option was NOT chosen...
				// skip its dependents (no validation)
				$skip = array_merge($skip, $events);
			}
		}

		if (!empty($skip))
		{
			foreach ($fields as $field)
			{
				if (in_array($field->get('name'), $skip))
				{
					// Temporarily mark as optional
					$field->set('action_create', self::STATE_OPTIONAL);
					$field->set('action_update', self::STATE_OPTIONAL);
					$field->set('action_edit', self::STATE_OPTIONAL);
				}
			}
		}

		return $fields;
	}

	/**
	 * Helper method to convert list of fields and field options to XML
	 *
	 * @param   mixed   $fields  Iterable object or array
	 * @param   string  $action
	 * @param   array   $data
	 * @return  string
	 */
	public static function toXml($fields, $action = null, $data = array())
	{
		$fields = self::resolveDependencies($fields, $data);

		// Convert to XML so we can use the Form processor
		$xml   = array();
		$xml[] = '<?xml version="1.0" encoding="utf-8"?>';
		$xml[] = '<form>';
		$xml[] = '<fieldset name="basic">';
		foreach ($fields as $field)
		{
			$f  = '<field type="' . $field->get('type') . '" name="' . htmlspecialchars($field->get('name'), ENT_COMPAT) . '" label="' . htmlspecialchars($field->get('label'), ENT_COMPAT) . '"';
			$f .= ($field->get('description')  ? ' description="' . htmlspecialchars($field->get('description'), ENT_COMPAT) . '"' : '');
			$f .= ($field->get('option_blank') ? ' option_blank="1"' : '');
			$f .= ($field->get('option_other') ? ' option_other="1"' : '');
			$f .= (!is_null($field->get('min')) ? ' min="' . (int) $field->get('min') . '"' : '');
			$f .= (!is_null($field->get('max')) ? ' max="' . (int) $field->get('max') . '"' : '');
			if (in_array($action, array('create', 'update', 'edit')))
			{
				$f .= ($field->get('action_' . $action) == self::STATE_REQUIRED ? ' required="true"' : '');
			}
			$f .= '>';

			$xml[] = $f;
			foreach ($field->options as $option)
			{
				$xml[] = '<option value="' . htmlspecialchars($option->get('value'), ENT_COMPAT) . '">' . htmlspecialchars($option->get('label'), ENT_COMPAT) . '</option>';
			}
			$xml[] = '</field>';
		}
		$xml[] = '</fieldset>';
		$xml[] = '</form>';

		return implode('', $xml);
	}
}
