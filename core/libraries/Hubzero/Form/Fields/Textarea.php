<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Supports a multi line area for entry of plain text
 */
class Textarea extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Textarea';

    /**
     * Method to get the textarea field input markup.
     * Use the rows and columns attributes to specify the dimensions of the area.
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
            $class    = trim('textarea textarea-bordered w-full' . ($xmlClass ? ' ' . $xmlClass : ''));
        } else {
            $class = $xmlClass;
        }

        $attributes = array(
            'type'         => 'text',
            'name'         => $this->name,
            'id'           => $this->id,
            'class'        => $class,
            'cols'         => ($this->element['cols'] ? (int) $this->element['cols'] : ''),
            'rows'         => ($this->element['rows'] ? (int) $this->element['rows'] : ''),
            'disabled'     => ((string) $this->element['disabled'] == 'true'    ? 'disabled' : ''),
            'onchange'     => (!$isDaisyui && $this->element['onchange']
                                  ? (string) $this->element['onchange'] : '')
        );

        $attr = array();
        foreach ($attributes as $key => $value) {
            if (!$value) {
                continue;
            }

            $attr[] = $key . '="' . $value . '"';
        }
        $attr = implode(' ', $attr);

        if ($isDaisyui && $this->element['onchange']) {
            $attr .= self::cspDataAttr((string) $this->element['onchange']);
        }

        return '<textarea ' .
            $attr .
            '>' .
            htmlspecialchars($this->value == null ? '' : $this->value, ENT_COMPAT, 'UTF-8') .
            '</textarea>';
    }
}
