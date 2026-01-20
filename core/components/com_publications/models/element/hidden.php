<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;

/**
 * Renders a hidden element
 */
class Hidden extends Base
{
    /**
  * Element name
  *
  * @var  string
  */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name = 'Hidden';

    /**
     * Return any options this element may have
     *
     * @param   string  $name          Name of the field
     * @param   string  $value         Value to check against
     * @param   object  $element       Data Source Object.
     * @param   string  $control_name  Control name (eg, control[fieldname])
     * @return  string  HTML
     */
    public function fetchElement($name, $value, &$element, $control_name)
    {
        $class = (isset($element->class)) ? $element->class : 'text_area';

        $val = $value;
        $k = 0;
        if (isset($element->options)) {
            if (is_array($element->options)) {
                foreach ($element->options as $option) {
                    if ($k >= 1) {
                        break;
                    }

                    $val  = $option->value;

                    $k++;
                }
            } elseif (is_object($element->options)) {
                $val  = $element->options->value;
            }
        }

        $fieldName = $control_name . '[' . $name . ']';
        $fieldId = $control_name . '-' . $name;
        return '<input type="hidden" name="' . $fieldName . '" id="' . $fieldId . '"'
            . ' value="' . $val . '" class="' . $class . '" />';
    }

    /**
     * Return any options this element may have
     *
     * @param   string  $label         Display name of the field
     * @param   string  $description   Description for the field
     * @param   object  $element       Data Source Object.
     * @param   string  $control_name  Control name (eg, control[fieldname])
     * @param   string  $name          Name of the field
     * @return  string  HTML
     */
    public function fetchTooltip($label, $description, &$element, $control_name = '', $name = '')
    {
        return '';
    }

    /**
     * Return any options this element may have
     *
     * @param   string  $name          Name of the field
     * @param   string  $value         Value to check against
     * @param   object  $element       Data Source Object.
     * @param   string  $control_name  Control name (eg, control[fieldname])
     * @return  string  HTML
     */
    public function fetchOptions($name, $value, &$element, $control_name)
    {
        $k = 0;
        $idBase = $control_name . '-' . $name;
        $nameBase = $control_name . '[' . $name . ']';

        $html[] = '<table class="admintable" id="' . $name . '">';
        $html[] = '<tbody>';
        if (isset($element->options) && is_array($element->options)) {
            foreach ($element->options as $option) {
                if ($k >= 1) {
                    break;
                }

                $labelId = $idBase . '-label-' . $k;
                $inputName = $nameBase . '[options][' . $k . '][label]';
                $html[] = '<tr>';
                $html[] = '<td><label for="' . $labelId . '">'
                    . Lang::txt('COM_PUBLICATIONS_VALUE') . '</label></td>';
                $html[] = '<td><input type="text" size="35" name="' . $inputName . '" id="' . $labelId . '"'
                    . ' value="' . $option->label . '" /></td>';
                $html[] = '</tr>';

                $k++;
            }
        }
        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode("\n", $html);
    }

    /**
     * Display the language for a language code
     *
     * @param   string  $value   Data
     * @return  string  Formatted string.
     */
    public function display($value)
    {
        return '';
    }
}
