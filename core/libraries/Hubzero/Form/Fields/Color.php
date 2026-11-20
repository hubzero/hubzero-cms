<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Html\Builder\Behavior;

/**
 * This implementation is designed to be compatible with HTML5's <input type="color">
 */
class Color extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Color';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $element = $this->fieldElement();

        if (empty($this->value)) {
            // A color field can't be empty, we default to black. This is the same as the HTML5 spec.
            $this->value = '#000000';
        }

        $this->value = '#' . ltrim($this->value, '#');

        // Initialize some field attributes.
        $attributes = [
            'type'         => 'text',
            'value'        => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
            'name'         => $this->name,
            'id'           => $this->id,
            'size'         => ($element['size'] ? (int) $element['size'] : ''),
            'maxlength'    => ($element['maxlength'] ? (int) $element['maxlength'] : ''),
            'class'        => ($element['class'] ? (string) $element['class'] : ''),
            'autocomplete' => ((string) $element['autocomplete'] == 'off' ? 'off' : ''),
            'readonly'     => ((string) $element['readonly'] == 'true' ? 'readonly' : ''),
            'disabled'     => ((string) $element['disabled'] == 'true' ? 'disabled' : ''),
            'onchange'     => ($element['onchange'] ? (string) $element['onchange'] : ''),
        ];

        if (!$attributes['disabled']) {
            Behavior::colorpicker();

            $attributes['class'] .= ' input-colorpicker';
        }

        $attr = [];
        foreach ($attributes as $key => $val) {
            if (!$val) {
                continue;
            }
            $attr[] = $key . '="' . $val . '"';
        }

        return '<span class="input-color"><input ' . implode(' ', $attr) . ' /></span>';
    }
}
