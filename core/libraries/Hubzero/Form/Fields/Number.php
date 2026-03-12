<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
        // Initialize some field attributes.
        $isDaisyui = \Hubzero\Facades\Document::getCssFramework() === 'daisyui';
        $xmlClass = $this->element['class'] ? (string) $this->element['class'] : '';
        if ($isDaisyui) {
            $xmlClass = trim(str_replace('inputbox', '', $xmlClass));
            $class = trim('input input-bordered input-sm w-24' . ($xmlClass ? ' ' . $xmlClass : ''));
        } else {
            $class = $xmlClass ?: null;
        }

        $attributes = array(
            'type'         => 'number',
            'value'        => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
            'name'         => $this->name,
            'id'           => $this->id,
            'min'          => (!is_null($this->element['min']) ? (int) $this->element['min'] : null),
            'max'          => ($this->element['max'] ? (int) $this->element['max'] : null),
            'step'         => ($this->element['step'] ? (int) $this->element['step'] : null),
            'pattern'      => ($this->element['pattern'] ? $this->element['pattern'] : null),
            'class'        => $class,
            'readonly'     => ((string) $this->element['readonly'] == 'true' ? 'readonly' : null),
            'disabled'     => ((string) $this->element['disabled'] == 'true' ? 'disabled' : null),
            'onchange'     => (!$isDaisyui && $this->element['onchange'] ? (string) $this->element['onchange'] : null)
        );

        $attr = array();
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

        if ($isDaisyui && $this->element['onchange']) {
            $attr .= self::cspDataAttr((string) $this->element['onchange']);
        }

        return '<input ' . $attr . ' />';
    }
}
