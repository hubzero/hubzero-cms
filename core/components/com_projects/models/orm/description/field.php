<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm\Description;

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
		return $this->oneToMany(__NAMESPACE__ . '\\Option', 'field_id');
	}

	/**
	 * Get child fields
	 *
	 * @return  object
	 */
	public function children()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Field', 'parent');
	}

	/**
	 * Get parent field
	 *
	 * @return  object
	 */
	public function parent()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Field', 'parent');
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
			case 'create':
				$index = 0;
			break;
			case 'proxy':
				$index = 1;
			break;
			case 'update':
				$index = 2;
			break;
			case 'edit':
				$index = 3;
			break;
			default:
				$index = 0;
			break;
		}

		$configured = Component::params('com_projects')->get($name);

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
			case 'R':
				$val = self::STATE_REQUIRED;
			break;
			case 'O':
				$val = self::STATE_OPTIONAL;
			break;
			case 'U':
				$val = self::STATE_READONLY;
			break;
			case 'H':
			case '-':
			default :
				$val = self::STATE_HIDDEN;
			break;
		}

		return $val;
	}

	/**
	 * Helper method to convert list of fields and field options to XML
	 *
	 * @param   mixed   $fields  Iterable object or attay
	 * @param   string  $action
	 * @return  string
	 */
	public static function toXml($fields, $action = null)
	{
		// Convert to XML so we can use the Form processor
		$xml   = array();
		$xml[] = '<?xml version="1.0" encoding="utf-8"?>';
		$xml[] = '<form>';
		$xml[] = '<fieldset name="basic">';
		foreach ($fields as $field)
		{
			$f  = '<field type="' . $field->get('type') . '" name="' . $field->get('name') . '" label="' . $field->get('label') . '"';
			$f .= ($field->get('description')  ? ' description="' . $field->get('description') . '"' : '');
			$f .= ($field->get('option_blank') ? ' option_blank="1"' : '');
			$f .= ($field->get('option_other') ? ' option_other="1"' : '');
			if (in_array($action, array('create', 'update', 'edit')))
			{
				$f .= ($field->get('action_' . $action) == self::STATE_REQUIRED ? ' required="true"' : '');
			}
			$f .= '>';

			$xml[] = $f;
			foreach ($field->options as $option)
			{
				$xml[] = '<option value="' . $option->get('value') . '">' . $option->get('label') . '</option>';
			}
			$xml[] = '</field>';
		}
		$xml[] = '</fieldset>';
		$xml[] = '</form>';

		return implode('', $xml);
	}
}
