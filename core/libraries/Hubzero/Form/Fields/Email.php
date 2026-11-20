<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Provides and input field for e-mail addresses
 */
class Email extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Email';

    /**
     * Method to get the field input markup for e-mail addresses.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $element = $this->fieldElement();

        // Initialize some field attributes.
        $size = $element['size'] ? ' size="' . (int) $element['size'] . '"' : '';
        $maxLength = $element['maxlength'] ? ' maxlength="' . (int) $element['maxlength'] . '"' : '';
        $class = $element['class'] ? ' ' . (string) $element['class'] : '';
        $readonly = ((string) $element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $disabled = ((string) $element['disabled'] == 'true') ? ' disabled="disabled"' : '';

        // " and \ are always forbidden in email unless escaped.
        $this->value = str_replace(['"','\\'], '', $this->value);

        // Initialize JavaScript field attributes.
        $onchange = $element['onchange'] ? ' onchange="' . (string) $element['onchange'] . '"' : '';

        return '<input type="text" name="'
            . $this->name
            . '" class="validate-email'
            . $class
            . '" id="'
            . $this->id
            . '"'
            . ' value="'
                . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')
                . '"'
                . $size
                . $disabled
                . $readonly
                . $onchange
                . $maxLength
                . '/>';
    }
}
