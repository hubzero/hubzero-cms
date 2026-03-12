<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
        if (empty($this->value)) {
            // A color field can't be empty, we default to black. This is the same as the HTML5 spec.
            $this->value = '#000000';
        }

        $this->value = '#' . ltrim($this->value, '#');

        // Initialize some field attributes.
        $isDaisyui = \Hubzero\Facades\Document::getCssFramework() === 'daisyui';
        $xmlClass = $this->element['class'] ? (string) $this->element['class'] : '';
        if ($isDaisyui) {
            $xmlClass = trim(str_replace('inputbox', '', $xmlClass));
            $class = trim('input input-bordered input-sm w-24' . ($xmlClass ? ' ' . $xmlClass : ''));
        } else {
            $class = $xmlClass;
        }

        $attributes = array(
            'type'         => 'text',
            'value'        => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
            'name'         => $this->name,
            'id'           => $this->id,
            'size'         => ($this->element['size']      ? (int) $this->element['size']      : ''),
            'maxlength'    => ($this->element['maxlength'] ? (int) $this->element['maxlength'] : ''),
            'class'        => $class,
            'autocomplete' => ((string) $this->element['autocomplete'] == 'off' ? 'off'      : ''),
            'readonly'     => ((string) $this->element['readonly'] == 'true'    ? 'readonly' : ''),
            'disabled'     => ((string) $this->element['disabled'] == 'true'    ? 'disabled' : ''),
            'onchange'     => (!$isDaisyui && $this->element['onchange']
                ? (string) $this->element['onchange'] : '')
        );

        if (!$attributes['disabled']) {
            Behavior::colorpicker();

            $attributes['class'] .= ' input-colorpicker';
        }

        $attr = array();
        foreach ($attributes as $key => $val) {
            if (!$val) {
                continue;
            }
            $attr[] = $key . '="' . $val . '"';
        }

        $attrStr = implode(' ', $attr);

        if ($isDaisyui && $this->element['onchange']) {
            $attrStr .= self::cspDataAttr((string) $this->element['onchange']);
        }

        return '<span class="input-color"><input ' . $attrStr . ' /></span>';
    }
}
