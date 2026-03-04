<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm;

use Hubzero\Database\Relational;
use Components\Groups\Models\Orm\Answer;
use Date;
use User;

include_once __DIR__ . DS . 'option.php';
include_once __DIR__ . DS . 'answer.php';

/**
 * Group description field model
 */
class Field extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xgroups_description';

	/**
	 * Field values for a group
	 *
	 * @var  array
	 */
	public $groupAnswers;

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
		'modified_by',
		'option_other',
		'option_blank',
		'required',
		'readonly',
		'disabled',
		'multiple',
		'parent_option'
	);

	/**
	 * Generates automatic option_other field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOptionOther($data)
	{
		if (!isset($data['option_other']))
		{
			$data['option_other'] = 0;
		}

		return (int) $data['option_other'];
	}

	/**
	 * Generates automatic option_blank field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOptionBlank($data)
	{
		if (!isset($data['option_blank']))
		{
			$data['option_blank'] = 0;
		}

		return (int) $data['option_blank'];
	}

	/**
	 * Generates automatic required field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticRequired($data)
	{
		if (!isset($data['required']))
		{
			$data['required'] = 0;
		}

		return (int) $data['required'];
	}

	/**
	 * Generates automatic readonly field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticReadonly($data)
	{
		if (!isset($data['readonly']))
		{
			$data['readonly'] = 0;
		}

		return (int) $data['readonly'];
	}

	/**
	 * Generates automatic disabled field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticDisabled($data)
	{
		if (!isset($data['disabled']))
		{
			$data['disabled'] = 0;
		}

		return (int) $data['disabled'];
	}

	/**
	 * Generates automatic multiple field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticMultiple($data)
	{
		if (!isset($data['multiple']))
		{
			$data['multiple'] = 0;
		}

		return (int) $data['multiple'];
	}

	/**
	 * Generates automatic parent_option field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticParentOption($data)
	{
		if (!isset($data['parent_option']))
		{
			$data['parent_option'] = 0;
		}

		return (int) $data['parent_option'];
	}

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
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return (isset($data['id']) && $data['id'] ? Date::of('now')->toSql() : null);
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array  $data  the data being saved
	 * @return  int
	 */
	public function automaticModifiedBy($data)
	{
		return (isset($data['id']) && $data['id'] ? User::get('id') : 0);
	}

	/**
	 * Get answers 
	 *
	 * @return  object
	 */
	public function answers()
	{
		return $this->oneToMany('Answer', 'field_id');
	}

	/**
	 * Get answers provided for specific group
	 *
	 * @param   integer  $group_id
	 * @return  object
	 */
	public function getGroupAnswers($group_id)
	{
		return $this->answers()->whereEquals('group_id', $group_id)->rows();
	}

	/**
	 * Get fields for group, including answers provided.
	 *
	 * @param   intenger  $group_id
	 * @return  object    Hubzero\Database\Rows
	 */
	public static function getAllGroupFields($group_id)
	{
		$fields = self::all()->including(['answers', function($answer) use ($group_id){
			return $answer->whereEquals('group_id', $group_id)->ordered();
		}])->ordered();

		return $fields;
	}

	/**
	 * Convert group answers to string or array if multiple values
	 *
	 * @param   integer  $group_id
	 * @return  mixed
	 */
	public function collectGroupAnswers($group_id)
	{
		$answersCollection = '';
		$groupAnswers = $this->getGroupAnswers($group_id);
		foreach ($groupAnswers as $answer)
		{
			if (empty($answersCollection))
			{
				$answersCollection = $answer->get('value');
			}
			else
			{
				$values = $answersCollection;
				if (!is_array($values))
				{
					$values = array($values);
				}
				$values[] = $answer->get('value');
				$answersCollection = $values;
			}
		}
		return $answersCollection;
	}

	/**
	 * Push answers provided via the for to the attributes property
	 *
	 * @param   mixed  $answers
	 * @return  void
	 */
	public function setFormAnswers($answers)
	{
		$fieldName = $this->get('name');
		$formAnswers = !empty($answers[$fieldName]) ? $answers[$fieldName] : '';

		if (!is_array($formAnswers))
		{
			$formAnswers = array($formAnswers);
		}

		if ($this->get('option_other'))
		{
			$otherName = $fieldName . '_other';
			if (!empty($answers[$otherName]))
			{
				$otherValue = array_pop($formAnswers);
				$otherValue = empty($otherValue) ? $answers[$otherName] : $otherValue;
				array_push($formAnswers, $otherValue);
			}
		}
		$this->set('formAnswers', $formAnswers);
		$this->setAnswersRule();
	}

	/**
	 * save all custom fields provided for the group
	 *
	 * @param   integer  $group_id
	 * @return  void
	 */
	public function saveGroupAnswers($group_id)
	{
		$this->setAnswersRule();

		if (!$this->validate())
		{
			return false;
		}

		$groupAnswers = $this->getGroupAnswers($group_id);
		$formAnswers = $this->get('formAnswers');
		$formAnswers = !is_array($formAnswers) ? array($formAnswers) : $formAnswers;

		foreach ($groupAnswers as $answer)
		{
			$answerValue = $answer->get('value');
			if (in_array($answerValue, $formAnswers))
			{
				$answerKey = array_search($answerValue, $formAnswers);
				unset($formAnswers[$answerKey]);
			}
			else
			{
				$answer->destroy();
			}
		}

		foreach ($formAnswers as $value)
		{
			$answerObj = Answer::blank()
				->set('group_id', $group_id)
				->set('value', $value)
				->set('field_id', $this->get('id'));
			$groupAnswers->push($answerObj);
		}

		$groupAnswers->save();

		return true;
	}

	/**
	 * Sets rule requiring formAnswers if required is set to true
	 *
	 * @return 	void
	 */
	protected function setAnswersRule()
	{
		if (!$this->hasAttribute('formAnswers'))
		{
			$this->set('formAnswers', '');
		}

		$currentRules = $this->getRules();

		if (!isset($currentRules['formAnswers']))
		{
			$this->addRule('formAnswers', function($data){
				if (isset($data['required']) && $data['required'] == 1)
				{
					if (empty($data['formAnswers']))
					{
						return Lang::txt('COM_GROUPS_CUSTOMFIELDS_REQUIRED', $data['label']);
					}
				}
				return false;
			});
		}
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
	 * Render the value
	 *
	 * This will load the field type and check if
	 * it has a custom renderer
	 *
	 * @param   integer  $careerplan_id
	 * @return  mixed
	 */
	public function getValue($careerplan_id)
	{
		$result = null;

		\Hubzero\Form\Helper::addFieldPath(__DIR__ . '/fields');

		if ($type = \Hubzero\Form\Helper::loadFieldType($this->get('type'), true))
		{
			if (method_exists($type, 'getValue'))
			{
				$result = $type->getValue($careerplan_id);
			}
		}

		return $result;
	}

	/**
	 * Render the value
	 *
	 * This will load the field type and check if
	 * it has a custom renderer
	 *
	 * @return  mixed
	 */
	public function renderValue($value)
	{
		$value = (is_array($value) && count($value) > 0) ? $value[0] : $value;
		if (empty($value))
		{
			return false;
		}

		\Hubzero\Form\Helper::addFieldPath(__DIR__ . '/fields');

		if ($type = \Hubzero\Form\Helper::loadFieldType($this->get('type'), true))
		{
			if (method_exists($type, 'renderValue'))
			{
				$value = $type->renderValue($value);
			}
		}

		return $value;
	}

	/**
	 * Render the value
	 *
	 * This will load the field type and check if
	 * it has a custom renderer
	 *
	 * @return  mixed
	 */
	public function saveValue($value)
	{
		$result = null;

		\Hubzero\Form\Helper::addFieldPath(__DIR__ . '/fields');

		if ($type = \Hubzero\Form\Helper::loadFieldType($this->get('type'), true))
		{
			if (method_exists($type, 'saveValue'))
			{
				$result = $type->saveValue($value);
			}
		}

		return $result;
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
					$field->set('required', 0);
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
	public static function toXml($fields, $data = array())
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
			$f .= ($field->get('placeholder')  ? ' placeholder="' . htmlspecialchars($field->get('placeholder'), ENT_COMPAT) . '"' : '');
			$f .= ($field->get('validate')     ? ' validate="' . htmlspecialchars($field->get('validate'), ENT_COMPAT) . '"' : '');
			$f .= ' default="' . htmlspecialchars($field->get('default_value', ''), ENT_COMPAT) . '"';
			$f .= ($field->get('option_blank') ? ' option_blank="1"' : '');
			$f .= ($field->get('option_other') ? ' option_other="1"' : '');
			$f .= (!is_null($field->get('min')) ? ' min="' . (int) $field->get('min') . '"' : '');
			$f .= (!is_null($field->get('max')) ? ' max="' . (int) $field->get('max') . '"' : '');
			if ($field->get('type') == 'textarea')
			{
				$f .= ' cols="' . ($field->get('cols') ? (int) $field->get('cols') : 35) . '"';
				$f .= ' rows="' . ($field->get('rows') ? (int) $field->get('rows') : 5) . '"';
			}
			if ($field->get('type') == 'select')
			{
				$f .= $field->get('multiple') ? ' multiple="multiple"' : '';
			}
			$f .= ($field->get('required') ? ' required="true"' : '');
			$f .= '>';

			$xml[] = $f;
			foreach ($field->options()->ordered('ordering')->rows() as $option)
			{
				$dependents = json_decode($option->dependents);
				$dependents = implode(", ", $dependents);
				$dependents = !empty($dependents) ? ' data-dependents="' . htmlspecialchars($dependents) . '"' : '';
				$xml[] = '<option value="' . htmlspecialchars($option->get('value'), ENT_COMPAT) . '"' . $dependents . '>' . htmlspecialchars($option->get('label'), ENT_COMPAT) . '</option>';
			}
			$xml[] = '</field>';
		}
		$xml[] = '</fieldset>';
		$xml[] = '</form>';

		return implode('', $xml);
	}

	/**
	 * Load a record by name
	 *
	 * @param   string  $name
	 * @return  object
	 */
	public static function oneByName($name)
	{
		$row = self::all()
			->whereEquals('name', $name)
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
