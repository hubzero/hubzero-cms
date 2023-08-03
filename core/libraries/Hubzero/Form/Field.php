<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form;

use Hubzero\Utility\Str;
use SimpleXMLElement;
use ReflectionClass;
use Lang;

/**
 * Abstract Form Field class.
 *
 * Inspired by Joomla's JFormField class
 *
 * @todo  Rewrite all of this.
 */
abstract class Field
{
	/**
	 * The description text for the form field.  Usually used in tooltips.
	 *
	 * @var  string
	 */
	protected $description;

	/**
	 * The SimpleXMLElement object of the <field /> XML element that describes the form field.
	 *
	 * @var  object
	 */
	protected $element;

	/**
	 * The Form object of the form attached to the form field.
	 *
	 * @var  object
	 */
	protected $form;

	/**
	 * The form control prefix for field names from the Form object attached to the form field.
	 *
	 * @var  string
	 */
	protected $formControl;

	/**
	 * The hidden state for the form field.
	 *
	 * @var    boolean
	 */
	protected $hidden = false;

	/**
	 * True to translate the field label string.
	 *
	 * @var  boolean
	 */
	protected $translateLabel = true;

	/**
	 * True to translate the field description string.
	 *
	 * @var  boolean
	 */
	protected $translateDescription = true;

	/**
	 * The document id for the form field.
	 *
	 * @var  string
	 */
	protected $id;

	/**
	 * The input for the form field.
	 *
	 * @var  string
	 */
	protected $input;

	/**
	 * The label for the form field.
	 *
	 * @var  string
	 */
	protected $label;

	/**
	 * The multiple state for the form field.  If true then multiple values are allowed for the
	 * field.  Most often used for list field types.
	 *
	 * @var  boolean
	 */
	protected $multiple = false;

	/**
	 * The name of the form field.
	 *
	 * @var  string
	 */
	protected $name;

	/**
	 * The name of the field.
	 *
	 * @var  string
	 */
	protected $fieldname;

	/**
	 * The group of the field.
	 *
	 * @var  string
	 */
	protected $group;

	/**
	 * The required state for the form field.  If true then there must be a value for the field to
	 * be considered valid.
	 *
	 * @var  boolean
	 */
	protected $required = false;

	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type;

	/**
	 * The validation method for the form field.  This value will determine which method is used
	 * to validate the value for a field.
	 *
	 * @var  string
	 */
	protected $validate;

	/**
	 * The value of the form field.
	 *
	 * @var  mixed
	 */
	protected $value;

	/**
	 * The input placeholder
	 *
	 * @var  string
	 */
	protected $placeholder;

	/**
	 * The label's CSS class of the form field
	 *
	 * @var  mixed
	 */
	protected $labelClass;

	/**
	 * The count value for generated name field
	 *
	 * @var  integer
	 */
	protected static $count = 0;

	/**
	 * The string used for generated fields names
	 *
	 * @var  integer
	 */
	protected static $generated_fieldname = '__field';

	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   object  $form  The form to attach to the form field object.
	 * @return  void
	 */
	public function __construct($form = null)
	{
		// If there is a form passed into the constructor set the form and form control properties.
		if ($form instanceof Form)
		{
			$this->form = $form;
			$this->formControl = $form->getFormControl();
		}

		// Detect the field type if not set
		if (!isset($this->type))
		{
			// Get the reflection info
			$r = new ReflectionClass($this);

			// Is it namespaced?
			if ($r->inNamespace())
			{
				// It is! This makes things easy.
				$this->type = $r->getShortName();
			}
			else
			{
				// We'll assume a CamelCased name
				// Split by words and take the last one
				$parts = Str::splitCamel(get_class($this));

				if ($parts[0] == 'J')
				{
					$this->type = Str::ucfirst($parts[count($parts) - 1], '_');
				}
				else
				{
					$this->type = Str::ucfirst($parts[0], '_') . Str::ucfirst($parts[count($parts) - 1], '_');
				}
			}
		}
	}

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 * @return  mixed   The property value or null.
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'class':
			case 'description':
			case 'formControl':
			case 'hidden':
			case 'id':
			case 'multiple':
			case 'name':
			case 'required':
			case 'type':
			case 'validate':
			case 'value':
			case 'labelClass':
			case 'fieldname':
			case 'group':
				return $this->$name;
				break;

			case 'input':
				// If the input hasn't yet been generated, generate it.
				if (empty($this->input))
				{
					$this->input = $this->getInput();
				}

				return $this->input;
				break;

			case 'label':
				// If the label hasn't yet been generated, generate it.
				if (empty($this->label))
				{
					$this->label = $this->getLabel();
				}

				return $this->label;
				break;
			case 'title':
				return $this->getTitle();
				break;
		}

		return null;
	}

	/**
	 * Method to attach a Form object to the field.
	 *
	 * @param   object  $form  The Form object to attach to the form field.
	 * @return  object  The form field object so that the method can be used in a chain.
	 */
	public function setForm(Form $form)
	{
		$this->form = $form;
		$this->formControl = $form->getFormControl();

		return $this;
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   object  &$element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed   $value     The form field value to validate.
	 * @param   string  $group     The field name group control value. This acts as as an array container for the field.
	 *                             For example if the field has name="foo" and the group value is set to "bar" then the
	 *                             full field name would end up being "bar[foo]".
	 * @return  boolean  True on success.
	 */
	public function setup(&$element, $value, $group = null)
	{
		// Make sure there is a valid JFormField XML element.
		if (!($element instanceof SimpleXMLElement) || (string) $element->getName() != 'field')
		{
			return false;
		}

		// Reset the input and label values.
		$this->input = null;
		$this->label = null;

		// Set the XML element object.
		$this->element = $element;
		// Get some important attributes from the form field element.
		$class = (string) $element['class'];
		$id = (string) $element['id'];
		$multiple = (string) $element['multiple'];
		$name = (string) $element['name'];
		$required = (string) $element['required'];

		// Set the required and validation options.
		$this->required = ($required == 'true' || $required == 'required' || $required == '1');
		$this->validate = (string) $element['validate'];

		// Add the required class if the field is required.
		if ($this->required)
		{
			if ($class)
			{
				if (strpos($class, 'required') === false)
				{
					$this->element['class'] = $class . ' required';
				}
			}
			else
			{
				$this->element->addAttribute('class', 'required');
			}
		}

		// Set the multiple values option.
		$this->multiple = ($multiple == 'true' || $multiple == 'multiple');

		// Allow for field classes to force the multiple values option.
		if (isset($this->forceMultiple))
		{
			$this->multiple = (bool) $this->forceMultiple;
		}

		// Set the field description text.
		$this->description = (string) $element['description'];

		// Set the visibility.
		$this->hidden = ((string) $element['type'] == 'hidden' || (string) $element['hidden'] == 'true');

		// Determine whether to translate the field label and/or description.
		$this->translateLabel = !((string) $this->element['translate_label'] == 'false' || (string) $this->element['translate_label'] == '0');
		$this->translateDescription = !((string) $this->element['translate_description'] == 'false'
			|| (string) $this->element['translate_description'] == '0');

		// Set the group of the field.
		$this->group = $group;

		// Set the field name and id.
		$this->fieldname = $this->getFieldName($name);
		$this->name      = $this->getName($this->fieldname);
		$this->id        = $this->getId($id, $this->fieldname);

		// Set the field default value.
		$this->value = $value;

		// Set the field placeholder
		$this->placeholder = isset($element['placeholder']) ? (string) $element['placeholder'] : '';

		// Set the CSS class of field label
		$this->labelClass = (string) $element['labelclass'];

		return true;
	}

	/**
	 * Method to get the id used for the field input tag.
	 *
	 * @param   string  $fieldId    The field element id.
	 * @param   string  $fieldName  The field element name.
	 * @return  string  The id to be used for the field input tag.
	 */
	protected function getId($fieldId, $fieldName)
	{
		// Initialise variables.
		$id = '';

		// If there is a form control set for the attached form add it first.
		if ($this->formControl)
		{
			$id .= $this->formControl;
		}

		// If the field is in a group add the group control to the field id.
		if ($this->group)
		{
			// If we already have an id segment add the group control as another level.
			if ($id)
			{
				$id .= '_' . str_replace('.', '_', $this->group);
			}
			else
			{
				$id .= str_replace('.', '_', $this->group);
			}
		}

		// If we already have an id segment add the field id/name as another level.
		if ($id)
		{
			$id .= '_' . ($fieldId ? $fieldId : $fieldName);
		}
		else
		{
			$id .= ($fieldId ? $fieldId : $fieldName);
		}

		// Clean up any invalid characters.
		$id = preg_replace('#\W#', '_', $id);

		return $id;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	abstract protected function getInput();

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 */
	protected function getTitle()
	{
		// Initialise variables.
		$title = '';

		if ($this->hidden)
		{

			return $title;
		}

		// Get the label text from the XML element, defaulting to the element name.
		$title = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$title = $this->translateLabel ? Lang::txt($title) : $title;

		return $title;
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 */
	protected function getLabel()
	{
		// Initialise variables.
		$label = '';

		if ($this->hidden)
		{
			return $label;
		}

		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? Lang::txt($text) : $text;

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTip' : '';
		$class = $this->required == true ? $class . ' required-field' : $class;
		$class = !empty($this->labelClass) ? $class . ' ' . $this->labelClass : $class;

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description))
		{
			$label .= ' title="'
				. htmlspecialchars(
				trim($text, ':') . '::' . ($this->translateDescription ? Lang::txt($this->description) : $this->description),
				ENT_COMPAT, 'UTF-8'
			) . '"';
		}

		// Add the label text and closing tag.
		if ($this->required)
		{
			$label .= '>' . $text . ' <span class="required star">' . Lang::txt('JOPTION_REQUIRED'). '</span></label>';
		}
		else
		{
			$label .= '>' . $text . '</label>';
		}

		return $label;
	}

	/**
	 * Method to get the name used for the field input tag.
	 *
	 * @param   string  $fieldName  The field element name.
	 * @return  string  The name to be used for the field input tag.
	 */
	protected function getName($fieldName)
	{
		// Initialise variables.
		$name = '';

		// If there is a form control set for the attached form add it first.
		if ($this->formControl)
		{
			$name .= $this->formControl;
		}

		// If the field is in a group add the group control to the field name.
		if ($this->group)
		{
			// If we already have a name segment add the group control as another level.
			$groups = explode('.', $this->group);
			if ($name)
			{
				foreach ($groups as $group)
				{
					$name .= '[' . $group . ']';
				}
			}
			else
			{
				$name .= array_shift($groups);
				foreach ($groups as $group)
				{
					$name .= '[' . $group . ']';
				}
			}
		}

		// If we already have a name segment add the field name as another level.
		if ($name)
		{
			$name .= '[' . $fieldName . ']';
		}
		else
		{
			$name .= $fieldName;
		}

		// If the field should support multiple values add the final array segment.
		if ($this->multiple)
		{
			$name .= '[]';
		}

		return $name;
	}

	/**
	 * Method to get the field name used.
	 *
	 * @param   string  $fieldName  The field element name.
	 * @return  string  The field name
	 */
	protected function getFieldName($fieldName)
	{
		if ($fieldName)
		{
			return $fieldName;
		}

		self::$count = self::$count + 1;
		return self::$generated_fieldname . self::$count;
	}

	/**
	 * Method to get an attribute of the field
	 *
	 * @param   string  $name     Name of the attribute to get
	 * @param   mixed   $default  Optional value to return if attribute not found
	 * @return  mixed             Value of the attribute / default
	 */
	public function getAttribute($name, $default = null)
	{
		if ($this->element instanceof SimpleXMLElement)
		{
			$attributes = $this->element->attributes();

			// Ensure that the attribute exists
			if (property_exists($attributes, $name))
			{
				$value = $attributes->$name;

				if ($value !== null)
				{
					return (string) $value;
				}
			}
		}

		return $default;
	}

	/**
	 * Simple method to set the value
	 *
	 * @param   mixed  $value  Value to set
	 * @return  void
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
}
