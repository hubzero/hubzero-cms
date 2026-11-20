<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Single check box field.
 * This is a boolean field with null for false and the specified option for true
 */
class Checkbox extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'Checkbox';

    /**
     * Method to get the field input markup.
     * The checked element sets the field to selected.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $element = $this->fieldElement();

        // Initialize some field attributes.
        $class    = $element['class'] ? ' class="' . (string) $element['class'] . '"' : '';
        $disabled = ((string) $element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $checked  = ((string) $element['value'] == $this->value) ? ' checked="checked"' : '';

        // Initialize JavaScript field attributes.
        $onclick = $element['onclick'] ? ' onclick="' . (string) $element['onclick'] . '"' : '';

        return '<input type="checkbox" name="'
            . $this->name
            . '" id="'
            . $this->id
            . '"'
            . ' value="'
            . htmlspecialchars((string) $element['value'], ENT_COMPAT, 'UTF-8')
            . '"'
            . $class
            . $checked
            . $disabled
            . $onclick
            . '/>';
    }
}
