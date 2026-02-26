<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;
use Hubzero\Facades\Lang;

/**
 * Renders a radio element
 */
class Radio extends Base
{
    /**
  * Element name
  *
  * @var string
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
            $output .= ' class="hasTip" title="' . Lang::txt($label) . '::' . Lang::txt($element->description) . '">';
        } else {
            $output .= '>';
        }
        $output .= Lang::txt($label);
        if (isset($element->required) && $element->required) {
            $output .= ' <span class="required">' . Lang::txt('JOPTION_REQUIRED') . '</span>';
        }
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

                $optionId = $control_name . '-' . $name . $option->value;
                $inputName = $control_name . '[' . $name . ']';
                $html[] = '<label for="' . $optionId . '">';
                $html[] = '<input class="option" type="radio" name="' . $inputName . '" id="' . $optionId . '"'
                    . ' value="' . $option->value . '"' . $sel . ' />';
                $html[] = Lang::txt($option->label) . '</label>';

                $k++;
            }
        }
        $html[] = '</fieldset>';

        return implode("\n", $html);
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
        $idBase = $control_name . '-' . $name;
        $nameBase = $control_name . '[' . $name . ']';

        $html[] = '<table class="admintable" id="' . $name . '">';
        $html[] = '<tfoot>';
        $html[] = '<tr>';
        $buttonText = Lang::txt('COM_PUBLICATIONS_NEW_OPTION');
        $html[] = '<td colspan="4" class="option-button">'
            . '<button rel="' . $name . '" class="add-custom-option"><span>' . $buttonText . '</span></button></td>';
        $html[] = '</tr>';
        $html[] = '</tfoot>';
        $html[] = '<tbody>';
        if (isset($element->options) && is_array($element->options)) {
            foreach ($element->options as $option) {
                $labelId = $idBase . '-label-' . $k;
                $inputName = $nameBase . '[options][' . $k . '][label]';
                $html[] = '<tr>';
                $html[] = '<td><label for="' . $labelId . '">' . Lang::txt('Option') . '</label></td>';
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
}
