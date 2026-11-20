<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

        // Access::usergroup() lists the groups itself. <option /> children
        // are not listed; declaring any adds its "All groups" entry.
        $allowAll = false;
        foreach ($element->children() as $option) {
            if ($option->getName() == 'option') {
                $allowAll = true;
                break;
            }
        }

        return Access::usergroup($this->name, $this->value, $attr, $allowAll);
    }
}
