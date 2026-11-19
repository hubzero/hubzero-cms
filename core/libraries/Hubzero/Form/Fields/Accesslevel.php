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
        $element = $this->fieldElement();

        // Initialize variables.
        $attr = '';

        // Initialize some field attributes.
        $attr .= $element['class'] ? ' class="' . (string) $element['class'] . '"' : '';
        $attr .= ((string) $element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $attr .= $element['size'] ? ' size="' . (int) $element['size'] . '"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $element['onchange'] ? ' onchange="' . (string) $element['onchange'] . '"' : '';

        // Get the field options.
        $options = $this->getOptions();

        return Access::level($this->name, $this->value, $attr, $options, $this->id);
    }
}
