<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Supports a numeric field.
 */
class Number extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Number';

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
            'type'         => 'number',
            'value'        => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
            'name'         => $this->name,
            'id'           => $this->id,
            'min'          => (!is_null($element['min']) ? (int) $element['min'] : null),
            'max'          => ($element['max'] ? (int) $element['max'] : null),
            'step'         => ($element['step'] ? (int) $element['step'] : null),
            'pattern'      => ($element['pattern'] ? $element['pattern'] : null),
            'class'        => ($element['class'] ? (string) $element['class'] : null),
            'readonly'     => ((string) $element['readonly'] == 'true' ? 'readonly' : null),
            'disabled'     => ((string) $element['disabled'] == 'true' ? 'disabled' : null),
            'onchange'     => ($element['onchange'] ? (string) $element['onchange'] : null),
        ];

        $attr = [];
        foreach ($attributes as $key => $value) {
            if ($key != 'value' && $key != 'min' && !$value) {
                continue;
            }
            if ($key == 'min' && is_null($value)) {
                continue;
            }

            $attr[] = $key . '="' . $value . '"';
        }
        $attr = implode(' ', $attr);

        return '<input ' . $attr . ' />';
    }
}
