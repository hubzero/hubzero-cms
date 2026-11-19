<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Behavior;

/**
 * Implements a combo box field.
 */
class Combo extends Select
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'Combo';

    /**
     * Method to get the field input markup for a combo box field.
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
        $attr .= $element['class'] ? ' class="combobox '
            . (string) $element['class']
            . '"' : ' class="combobox"';
        $attr .= ((string) $element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $attr .= ((string) $element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $attr .= $element['size'] ? ' size="' . (int) $element['size'] . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $element['onchange'] ? ' onchange="' . (string) $element['onchange'] . '"' : '';

        // Get the field options.
        $options = $this->getOptions();

        // Load the combobox behavior.
        Behavior::combobox();

        // Build the input for the combo box.
        $html[] = '<input type="text" name="'
            . $this->name
            . '" id="'
            . $this->id
            . '"'
            . ' value="'
            . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8')
            . '"'
            . $attr
            . '/>';

        // Build the list for the combo box.
        $html[] = '<ul id="combobox-' . $this->id . '" style="display:none;">';
        foreach ($options as $option) {
            $html[] = '<li>' . $option->text . '</li>';
        }
        $html[] = '</ul>';

        return implode($html);
    }
}
