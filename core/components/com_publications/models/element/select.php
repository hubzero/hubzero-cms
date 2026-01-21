<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Element;

use Components\Publications\Models\Element as Base;
use Html;
use Lang;

/**
 * Renders a select list element
 */
class Select extends Base
{
    /**
  * Element type
  *
  * @var  string
  */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name = 'Select list';

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
        $class = (isset($element->class)) ? 'class="' . $element->class . '"' : 'class="inputbox"';

        $options = array();
        if (!$element->required) {
            $options[] = Html::select('option', '', Lang::txt('Select...'));
        }
        foreach ($element->options as $option) {
            $val    = $option->value;
            $text   = $option->label;
            $options[] = Html::select('option', $val, Lang::txt($text));
        }

        $fieldName = $control_name . '[' . $name . ']';
        $fieldId = $control_name . '-' . $name;
        $selectHtml = Html::select(
            'genericlist',
            $options,
            $fieldName,
            $class,
            'value',
            'text',
            $value,
            $fieldId
        );
        return '<span class="field-wrap">' . $selectHtml . '</span>';
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
        $captionText = Lang::txt('Lists include blank "Select..." option unless made a required field');
        $html[] = '<caption>' . $captionText . '</caption>';
        $html[] = '<tfoot>';
        $html[] = '<tr>';
        $buttonText = Lang::txt('COM_PUBLICATIONS_NEW_OPTION');
        $html[] = '<td colspan="4" class="option-button">'
            . '<button rel="' . $name . '" class="add-custom-option"><span>' . $buttonText . '</span></button></td>';
        $html[] = '</tr>';
        $html[] = '</tfoot>';
        $html[] = '<tbody>';
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
        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode("\n", $html);
    }
}
