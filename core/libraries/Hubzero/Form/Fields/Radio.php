<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Html\Builder\Select as Dropdown;
use Hubzero\Facades\App;

/**
 * Provides radio button inputs
 */
class Radio extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Radio';

    /**
     * Method to get the radio button field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        // Initialize variables.
        $html    = array();
        $isDaisyui = \Hubzero\Facades\Document::getCssFramework() === 'daisyui';

        // Initialize some field attributes.
        $fieldClass = $this->element['class'] ? (string) $this->element['class'] : '';
        if ($isDaisyui) {
            $fsClass = ' class="flex flex-col gap-1"';
        } else {
            $fsClass = $fieldClass ? ' class="radio ' . $fieldClass . '"' : ' class="radio"';
        }

        // Start the radio field output.
        $html[] = '<fieldset id="' . $this->id . '"' . $fsClass . '>';

        // Get the field options.
        $options = $this->getOptions();
        $found   = false;

        if (!$isDaisyui) {
            $html[] = '<ul>';
        }

        // Build the radio field output.
        foreach ($options as $i => $option) {
            // Initialize some option attributes.
            $checked  = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
            $optClass = !empty($option->class) ? $option->class : '';
            $disabled = !empty($option->disable) ? ' disabled="disabled"' : '';

            if ($checked) {
                $found = true;
            }

            // Add data attributes
            $dataAttributes = '';
            foreach ($option as $field => $value) {
                $dataField = strtolower(substr($field, 0, 4));
                if ($dataField == 'data') {
                    $dataAttributes .= ' ' . $field . '="' . $value . '"';
                }
            }
            // Initialize some JavaScript option attributes.
            $onclick = '';
            if (!empty($option->onclick)) {
                $onclick = $isDaisyui
                    ? self::cspDataAttr($option->onclick, 'onclick')
                    : ' onclick="' . $option->onclick . '"';
            }

            $optText = App::get('language')->alt(
                $option->text,
                preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)
            );

            if ($isDaisyui) {
                $inputClass = trim('radio radio-sm' . ($optClass ? ' ' . $optClass : ''));
                $html[] = '<label class="flex items-center gap-2 cursor-pointer text-sm">';
                $html[] = '<input type="radio" id="' . $this->id . $i .
                    '" name="' . $this->name .
                    '" value="' . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' .
                    $checked . $disabled . $onclick . $dataAttributes .
                    ' class="' . $inputClass . '" />';
                $html[] = $optText . '</label>';
            } else {
                $classAttr = $optClass ? ' class="' . $optClass . '"' : '';
                $html[] = '<li>';
                $html[] = '<input type="radio" id="' . $this->id . $i .
                    '" name="' . $this->name .
                    '" value="' . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' .
                    $checked . $classAttr . $onclick . $disabled . $dataAttributes . '/>';
                $html[] = '<label for="' . $this->id . $i . '"' . $classAttr . '>' . $optText . '</label>';
                $html[] = '</li>';
            }
        }

        if ($this->element['option_other']) {
            $checked  = '';
            $optClass = isset($optClass) ? $optClass : '';
            $onclick  = isset($onclick) ? $onclick : '';
            $disabled = isset($disabled) ? $disabled : '';
            if (!$found && $this->value) {
                $checked = ' checked="checked"';
            }
            $otherText = App::get('language')->txt('JOTHER');
            if ($isDaisyui) {
                $html[] = '<label class="flex items-center gap-2 cursor-pointer text-sm">';
                $html[] = '<input type="radio" id="' . $this->id . ($i + 1) .
                    '" name="' . $this->name . '" value=""' .
                    $checked . $disabled . $onclick . ' class="radio radio-sm" />';
                $html[] = $otherText . '</label>';
                $html[] = '<input type="text" id="' . $this->id . '_other" name="' .
                    $this->getName($this->fieldname . '_other') . '" value="' .
                    ($checked ? htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') : '') .
                    '" class="input input-bordered input-sm w-full" />';
            } else {
                $classAttr = $optClass ? ' class="' . $optClass . '"' : '';
                $html[] = '<li>';
                $html[] = '<input type="radio" id="' . $this->id . ($i + 1) .
                    '" name="' . $this->name . '" value=""' .
                    $checked . $classAttr . $onclick . $disabled . '/>';
                $html[] = '<label for="' . $this->id . ($i + 1) . '"' . $classAttr . '>' . $otherText . '</label>';
                $html[] = '<input type="text" id="' . $this->id . '_other" name="' .
                    $this->getName($this->fieldname . '_other') . '" value="' .
                    ($checked ? htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') : '') .
                    '"' . $classAttr . $onclick . $disabled . '/>';
                $html[] = '</li>';
            }
        }

        if (!$isDaisyui) {
            $html[] = '</ul>';
        }

        // End the radio field output.
        $html[] = '</fieldset>';

        return implode($html);
    }

    /**
     * Method to get the field options for radio buttons.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        // Initialize variables.
        $options = array();

        foreach ($this->element->children() as $option) {
            // Only add <option /> elements.
            if ($option->getName() != 'option') {
                continue;
            }

            $label = (isset($option[0]) ? $option[0] : $option['label']);

            // Create a new option object based on the <option /> element.
            $tmp = Dropdown::option(
                (string) $option['value'],
                trim((string) $label),
                'value',
                'text',
                ((string) $option['disabled'] == 'true')
            );
            foreach ($option->attributes() as $index => $value) {
                $dataCheck = strtolower(substr($index, 0, 4));
                if ($dataCheck == 'data') {
                    $tmp->$index = (string) $value;
                }
            }

            // Set some option attributes.
            $tmp->class = (string) $option['class'];

            // Set some JavaScript option attributes.
            $tmp->onclick = (string) $option['onclick'];

            // Add the option object to the result set.
            $options[] = $tmp;
        }

        reset($options);

        return $options;
    }
}
