<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Element;

use Components\Resources\Models\Element as Base;
use Hubzero\Facades\Lang;

/**
 * Renders a radio element
 */
class Radio extends Base
{
    /**
     * Element name
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name = 'Radio boxes';

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
    public function fetchElement($name, $value, &$element, $control_name)
    {
        $label = $element->label ? $element->label : $element->name;

        $html = array();
        $html[] = '<fieldset>';

        $output = '<legend id="' . $control_name . $name . '-lgd"';
        if (isset($element->description) && $element->description) {
            $output .= ' class="hasTip" title="' . $label . '::' . $element->description . '">';
        } else {
            $output .= '>';
        }
        $output .= $label;
        $isRequired = isset($element->required) && $element->required;
        $requiredSpan = ' <span class="required">' . Lang::txt('JOPTION_REQUIRED') . '</span>';
        $output .= $isRequired ? $requiredSpan : '';
        $output .= '</legend>';

        $html[] = $output;

        $k = 0;
        if (isset($element->options) && is_array($element->options)) {
            foreach ($element->options as $option) {
                $sel = '';
                if (is_array($value)) {
                    foreach ($value as $val) {
                        $k2 = is_object($val) ? $val->value : $val;
                        if ($value == $k2) {
                            $sel .= ' selected="selected"';
                            break;
                        }
                    }
                } else {
                    $sel .= ($option->value == $value ? ' checked="checked"' : '');
                }

                $inputId = $control_name . '-' . $name . $option->value;
                $inputName = $control_name . '[' . $name . ']';
                $html[] = '<label for="' . $inputId . '">';
                $html[] = '<input class="option" type="radio" name="' . $inputName
                    . '" id="' . $inputId . '" value="' . $option->value . '"' . $sel . ' />';
                $html[] = $option->label . '</label>';

                $k++;
            }
        }
        $html[] = '</fieldset>';

        return '<span class="field-wrap">' . implode("\n", $html) . '</span>';
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
        $html = array();

        $k = 0;

        $html[] = '<table class="admintable" id="' . $name . '">';
        $html[] = '<tfoot>';
        $html[] = '<tr>';
        $btnText = Lang::txt('COM_RESOURCES_NEW_OPTION');
        $html[] = '<td colspan="2" class="option-button"><button data-rel="' . $name
            . '" class="add-custom-option"><span>' . $btnText . '</span></button></td>';
        $html[] = '</tr>';
        $html[] = '</tfoot>';
        $html[] = '<tbody>';
        if (isset($element->options) && is_array($element->options)) {
            foreach ($element->options as $option) {
                $html[] = '<tr>';
                $labelId = $control_name . '-' . $name . '-label-' . $k;
                $optName = $control_name . '[' . $name . '][options][' . $k . '][label]';
                $optLabel = Lang::txt('COM_RESOURCES_OPTION');
                $html[] = '<td><label for="' . $labelId . '">' . $optLabel . '</label></td>';
                $html[] = '<td><input type="text" size="35" name="' . $optName . '" id="'
                    . $labelId . '" value="' . $option->label . '" /></td>';
                $html[] = '</tr>';

                $k++;
            }
        }
        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode("\n", $html);
    }
}
