<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Access;

/**
 * Provides a list of access levels. Access levels control what users in specific
 * groups can see.
 */
class AccessLevel extends Select
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'AccessLevel';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        // Initialize variables.
        $attr = '';

        // Initialize some field attributes.
        $isDaisyui = \Hubzero\Facades\Document::getCssFramework() === 'daisyui';
        $xmlClass = $this->element['class'] ? (string) $this->element['class'] : '';
        if ($isDaisyui) {
            $xmlClass = trim(str_replace('inputbox', '', $xmlClass));
            $cls = trim('select select-bordered select-sm w-full' . ($xmlClass ? ' ' . $xmlClass : ''));
        } else {
            $cls = $xmlClass;
        }
        $attr .= $cls ? ' class="' . $cls . '"' : '';
        $attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Initialize JavaScript field attributes.
        if ($this->element['onchange']) {
            $attr .= $isDaisyui
                ? self::cspDataAttr((string) $this->element['onchange'])
                : ' onchange="' . (string) $this->element['onchange'] . '"';
        }

        // Get the field options.
        $options = $this->getOptions();

        return Access::level($this->name, $this->value, $attr, $options, $this->id);
    }
}
