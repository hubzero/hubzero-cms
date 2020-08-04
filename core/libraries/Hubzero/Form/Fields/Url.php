<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

/**
 * Supports a URL text field
 */
class Url extends Text
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Url';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$attributes = array(
			'type'         => 'text',
			'value'        => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
			'name'         => $this->name,
			'id'           => $this->id,
			'placeholder'  => 'http://',
			'size'         => ($this->element['size']      ? (int) $this->element['size']      : ''),
			'maxlength'    => ($this->element['maxlength'] ? (int) $this->element['maxlength'] : ''),
			'class'        => ($this->element['class']     ? (string) $this->element['class']  : ''),
			'autocomplete' => ((string) $this->element['autocomplete'] == 'off' ? 'off'      : ''),
			'readonly'     => ((string) $this->element['readonly'] == 'true'    ? 'readonly' : ''),
			'disabled'     => ((string) $this->element['disabled'] == 'true'    ? 'disabled' : ''),
			'onchange'     => ($this->element['onchange']  ? (string) $this->element['onchange'] : '')
		);

		$attr = array();
		foreach ($attributes as $key => $value)
		{
			if ($key != 'value' && !$value)
			{
				continue;
			}

			$attr[] = $key . '="' . $value . '"';
		}
		$attr = implode(' ', $attr);

		return '<input ' . $attr . ' />';
	}
}
