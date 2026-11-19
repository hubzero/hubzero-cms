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
        $element = $this->fieldElement();

        // Initialize some field attributes.
        $attributes = [
            'type'         => 'text',
            'value'        => htmlspecialchars($this->value == null ? '' : $this->value, ENT_COMPAT, 'UTF-8'),
            'name'         => $this->name,
            'id'           => $this->id,
            'placeholder'  => 'http://',
            'size'         => ($element['size'] ? (int) $element['size'] : ''),
            'maxlength'    => ($element['maxlength'] ? (int) $element['maxlength'] : ''),
            'class'        => ($element['class'] ? (string) $element['class'] : ''),
            'autocomplete' => ((string) $element['autocomplete'] == 'off' ? 'off' : ''),
            'readonly'     => ((string) $element['readonly'] == 'true' ? 'readonly' : ''),
            'disabled'     => ((string) $element['disabled'] == 'true' ? 'disabled' : ''),
            'onchange'     => ($element['onchange'] ? (string) $element['onchange'] : ''),
        ];

        $attr = [];
        foreach ($attributes as $key => $value) {
            if ($key != 'value' && !$value) {
                continue;
            }

            $attr[] = $key . '="' . $value . '"';
        }
        $attr = implode(' ', $attr);

        return '<input ' . $attr . ' />';
    }
}
