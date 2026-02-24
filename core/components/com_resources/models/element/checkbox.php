<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Element;

use Components\Resources\Models\Element as Base;
use Lang;

/**
 * Renders a checkbox element
 */
class Checkbox extends Base
{
    /**
     * Element name
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name = 'Checkboxes';

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

        $values = array();

        $pattern = "/<\d>(.*?)<\/\d>/i";
        preg_match_all($pattern, $value, $matches);
        if ($matches) {
            foreach ($matches[1] as $match) {
                $pattern = "/<\d>$match<\/\d>/i";
                $value = preg_replace($pattern, '', $value);
                $values[] = $match;
            }
        }
        $values[] = $value;

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
                if (is_array($values)) {
                    foreach ($values as $val) {
                        $k2 = is_object($val) ? $val->value : $val;
                        if ($option->value == $k2) {
                            $sel .= ' checked="checked"';
                            break;
                        }
                    }
                } else {
                    $sel .= ($option->value == $value ? ' checked="checked"' : '');
                }

                $inputId = $control_name . '-' . $name . $option->value;
                $inputName = $control_name . '[' . $name . '][]';
                $html[] = '<label for="' . $inputId . '">';
                $html[] = '<input class="option" type="checkbox" name="' . $inputName
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

    /**
     * Display the language for a language code
     *
     * @param   string  $value   Data
     * @return  string  Formatted string.
     */
    public function display($value)
    {
        $values = array(
            '<ul>'
        );

        $pattern = "/<\d>(.*?)<\/\d>/i";
        preg_match_all($pattern, $value, $matches);
        if ($matches) {
            foreach ($matches[1] as $match) {
                $pattern = "/<\d>$match<\/\d>/i";
                $value = preg_replace($pattern, '', $value);
                $values[] = '<li>' . $match . '</li>';
            }
        }

        $values[] = '</ul>';

        return implode("\n", $values);
    }

    /**
     * Create html tag for element.
     *
     * @param   string  $tag     Tag Name
     * @param   sting   $value   Tag Value
     * @param   string  $prefix  Tag prefix
     * @return  string  HTML
     */
    public function toHtmlTag($tag, $value, $prefix = 'nb:')
    {
        // array to hold date parts
        $parts = array();

        // case value to array (in case object)
        $value = array_filter((array) $value);

        // loop through each value prop
        foreach ($value as $k => $v) {
            array_push($parts, "<{$k}>{$v}</{$k}>");
        }

        // build and return tag
        $html  = "<{$prefix}{$tag}>";
        $html .= implode("\n", $parts);
        $html .= "</{$prefix}{$tag}>";
        return $html;
    }
}
