<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
        // Initialize some field attributes.
        $size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
        $isDaisyui = \Hubzero\Facades\Document::getCssFramework() === 'daisyui';
        $xmlClass = $this->element['class'] ? (string) $this->element['class'] : '';
        if ($isDaisyui) {
            $xmlClass = trim(str_replace('inputbox', '', $xmlClass));
            $cls = trim('input input-bordered input-sm w-full validate-email' . ($xmlClass ? ' ' . $xmlClass : ''));
        } else {
            $cls = 'validate-email' . ($xmlClass ? ' ' . $xmlClass : '');
        }
        $readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

        // " and \ are always forbidden in email unless escaped.
        $this->value = str_replace(array('"','\\'), '', $this->value);

        // Initialize JavaScript field attributes.
        $onchange = '';
        if ($this->element['onchange']) {
            $onchange = $isDaisyui
                ? self::cspDataAttr((string) $this->element['onchange'])
                : ' onchange="' . (string) $this->element['onchange'] . '"';
        }

        return '<input type="text" name="' .
            $this->name .
            '" class="' .
            $cls .
            '" id="' .
            $this->id .
            '"' .
            ' value="'
            .
                htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') .
                '"' .
                $size .
                $disabled .
                $readonly .
                $onchange .
                $maxLength .
                '/>';
    }
}
