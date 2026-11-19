<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Provides a hidden field
 */
class Hidden extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Hidden';

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
            'type'         => 'hidden',
            'value'        => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
            'name'         => $this->name,
            'id'           => $this->id,
            'class'        => ($element['class'] ? (string) $element['class'] : ''),
            'autocomplete' => ((string) $element['autocomplete'] == 'off' ? 'off' : ''),
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
