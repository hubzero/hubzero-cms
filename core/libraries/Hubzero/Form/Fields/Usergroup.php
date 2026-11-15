<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Html\Builder\Access;

/**
 * Supports a nested check box field listing user groups.
 * Multiselect is available by default.
 */
class Usergroup extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Usergroup';

    /**
     * Method to get the user group field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        // Initialize variables.
        $attr = '';

        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
        $attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

        // Access::usergroup() lists the groups itself. <option /> children
        // are not listed; declaring any adds its "All groups" entry.
        $allowAll = false;
        foreach ($this->element->children() as $option) {
            if ($option->getName() == 'option') {
                $allowAll = true;
                break;
            }
        }

        return Access::usergroup($this->name, $this->value, $attr, $allowAll);
    }
}
