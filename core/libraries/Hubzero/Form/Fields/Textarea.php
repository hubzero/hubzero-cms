<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
        $element = $this->fieldElement();

        // Initialize some field attributes.
        $attributes = [
            'type'         => 'text',
            'name'         => $this->name,
            'id'           => $this->id,
            'class'        => ($element['class'] ? (string) $element['class'] : ''),
            'cols'         => ($element['cols'] ? (int) $element['cols'] : ''),
            'rows'         => ($element['rows'] ? (int) $element['rows'] : ''),
            'disabled'     => ((string) $element['disabled'] == 'true' ? 'disabled' : ''),
            'onchange'     => ($element['onchange'] ? (string) $element['onchange'] : ''),
        ];

        $attr = [];
        foreach ($attributes as $key => $value) {
            if (!$value) {
                continue;
            }

            $attr[] = $key . '="' . $value . '"';
        }
        $attr = implode(' ', $attr);

        return '<textarea '
            . $attr
            . '>'
            . htmlspecialchars($this->value == null ? '' : $this->value, ENT_COMPAT, 'UTF-8')
            . '</textarea>';
    }
}
