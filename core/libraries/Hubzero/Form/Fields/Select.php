<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Html\Builder\Select as Dropdown;
use Hubzero\Facades\App;

/**
 * Supports a generic list of options.
 */
class Select extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Select';

    /**
     * Method to get the field input markup for a generic list.
     * Use the multiple attribute to enable multiselect.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $element = $this->fieldElement();

        // Initialize variables.
        $html = [];
        $attr = '';

        // Initialize some field attributes.
        $attr .= $element['class'] ? ' class="' . (string) $element['class'] . '"' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ((string) $element['readonly'] == 'true' || (string) $element['disabled'] == 'true') {
            $attr .= ' disabled="disabled"';
        }

        $attr .= $element['size'] ? ' size="' . (int) $element['size'] . '"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $element['onchange'] ? ' onchange="' . (string) $element['onchange'] . '"' : '';

        // Get the field options.
        $options = (array) $this->getOptions();

        // Create a read-only list (no name) with a hidden input to store the value.
        if ((string) $element['readonly'] == 'true') {
            $html[] = Dropdown::genericlist($options, '', trim($attr), 'value', 'text', $this->value, $this->id);
            $html[] = '<input type="hidden" name="'
                . $this->name
                . '" value="'
                . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')
                . '" />';
        } else {
            // Create a regular list.
            if ($element['option_other']) {
                // Add "Other..." option to the dropdown
                $options[] = Dropdown::option('other', App::get('language')->txt('Other...'), 'value', 'text');

                $found = false;
                foreach ($options as $option) {
                    if ($option->value == $this->value) {
                        $found = true;
                    }
                }

                // If current value doesn't match any option, select "other"
                $selectValue = (!$found && $this->value) ? 'other' : $this->value;

                $html[] = Dropdown::genericlist(
                    $options,
                    $this->name,
                    trim($attr),
                    'value',
                    'text',
                    $selectValue,
                    $this->id,
                );

                $showOther = (!$found && $this->value) ? '' : ' style="display:none;"';
                $html[] = '<input type="text" class="option-other-input" name="'
                    . $this->getName($this->fieldname . '_other')
                    . '" value="'
                    . ($found ? '' : htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'))
                    . '" placeholder="'
                    . (empty($this->placeholder) ? App::get('language')->txt('Other...') : htmlspecialchars(
                        $this->placeholder,
                        ENT_COMPAT,
                        'UTF-8',
                    ))
                    . '" aria-label="'
                    . App::get('language')->txt('Other')
                    . '"'
                    . $showOther
                    . ' />';
            } else {
                $html[] = Dropdown::genericlist(
                    $options,
                    $this->name,
                    trim($attr),
                    'value',
                    'text',
                    $this->value,
                    $this->id,
                );
            }
        }

        return implode($html);
    }

    /**
     * Method to get the field options.
     *
     * @return  array<int, mixed>  The field option objects.
     */
    protected function getOptions()
    {
        $element = $this->fieldElement();

        // Initialize variables.
        $options = [];

        if ($element['option_blank']) {
            $options[] = Dropdown::option('', App::get('language')->txt('- Select -'), 'value', 'text');
        }

        foreach ($element->children() as $option) {
            // Only add <option /> elements.
            if ($option->getName() != 'option') {
                continue;
            }

            $label = (isset($option[0]) ? $option[0] : $option['label']);

            // Create a new option object based on the <option /> element.
            $tmp = Dropdown::option(
                (string) $option['value'],
                App::get('language')->alt(
                    trim((string) $label),
                    preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname),
                ),
                'value',
                'text',
                ((string) $option['disabled'] == 'true'),
            );
            foreach ($option->attributes() as $index => $value) {
                $dataCheck = strtolower(substr($index, 0, 4));
                if ($dataCheck == 'data') {
                    $tmp->$index = (string) $value;
                }
                if (!$this->value && $index == 'selected') {
                    $this->value = (string) $option['value'];
                }
            }

            // Set some option attributes.
            $tmp->set('class', (string) $option['class']);

            // Set some JavaScript option attributes.
            $tmp->set('onclick', (string) $option['onclick']);

            // Add the option object to the result set.
            $options[] = $tmp;
        }

        reset($options);

        return $options;
    }
}
