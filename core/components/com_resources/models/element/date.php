<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Element;

use Components\Resources\Models\Element as Base;
use stdClass;
use Hubzero\Facades\Lang;

/**
 * Renders a category element
 */
class Date extends Base
{
    /**
     * Element name
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name = 'Date';

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
        $c = 0;
        if (isset($element->year) && $element->year) {
            $c++;
        }
        if (isset($element->month) && $element->month) {
            $c++;
        }
        if (isset($element->day) && $element->day) {
            $c++;
        }

        if ($c <= 1) {
            return parent::fetchTooltip($label, $description, $element, $control_name, $name);
        }
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
        $html = array();

        $c = 0;
        if (isset($element->year) && $element->year) {
            $c++;
        }
        if (isset($element->month) && $element->month) {
            $c++;
        }
        if (isset($element->day) && $element->day) {
            $c++;
        }

        if ($c > 1) {
            $html[] = '<fieldset>';

            $label = $element->label ? $element->label : $element->name;

            $output = '<legend id="' . $control_name . $name . '-lgd"';
            if (isset($element->description) && $element->description) {
                $output .= ' class="hasTip" title="' . Lang::txt($label) . '::'
                . Lang::txt($element->description) . '">';
            } else {
                $output .= '>';
            }
            $output .= Lang::txt($label);
            $isRequired = isset($element->required) && $element->required;
            $requiredSpan = ' <span class="required">' . Lang::txt('JOPTION_REQUIRED') . '</span>';
            $output .= $isRequired ? $requiredSpan : '';
            $output .= '</legend>';

            $html[] = $output;
        }

        if (isset($element->year) && $element->year) {
            $year = $this->getValue('year', $value);

            // Get the year range
            // 0 = start
            // 1 = end
            if ($element->options) {
                $k = 0;
                foreach ($element->options as $key => $option) {
                    if ($k == 0) {
                        $i = $option->value;
                    }
                    if ($k == 1) {
                        $y = $option->value;
                    }
                    $k++;
                }
            }

            // Set defaults if no date range available
            $i = (isset($i) && $i) ? $i : 1950;
            $y = (isset($y) && $y) ? $y : date("Y");

            // Build the list of years
            $options = array();
            $y++;
            for ($i, $n = $y; $i < $n; $i++) {
                $options[] = \Hubzero\Facades\Html::select('option', $i, $i);
            }

            $options = array_reverse($options);
            array_unshift($options, \Hubzero\Facades\Html::select('option', '0', Lang::txt('Year...')));

            $selectName = $control_name . '[' . $name . '][year]';
            $selectId = $control_name . '-' . $name . '-year';
            $html[] = \Hubzero\Facades\Html::select(
                'genericlist',
                $options,
                $selectName,
                'class="option"',
                'value',
                'text',
                $year,
                $selectId
            );
        }

        if (isset($element->month) && $element->month) {
            $month = $this->getValue('month', $value);

            // Build the list of years
            $options = array(
                \Hubzero\Facades\Html::select('option', '0', Lang::txt('Month...'))
            );
            $i = 1;
            $y = 13;
            for ($i, $n = $y; $i < $n; $i++) {
                $options[] = \Hubzero\Facades\Html::select('option', $i, $this->getMonth($i));
            }

            $selectName = $control_name . '[' . $name . '][month]';
            $selectId = $control_name . '-' . $name . '-month';
            $html[] = \Hubzero\Facades\Html::select(
                'genericlist',
                $options,
                $selectName,
                'class="option"',
                'value',
                'text',
                $month,
                $selectId
            );
        }

        if (isset($element->day) && $element->day) {
            $day = $this->getValue('day', $value);

            // Build the list of years
            $options = array(
                \Hubzero\Facades\Html::select('option', '0', Lang::txt('Day...'))
            );
            $i = 1;
            $y = 32;
            for ($i, $n = $y; $i < $n; $i++) {
                $options[] = \Hubzero\Facades\Html::select('option', $i, $i);
            }

            $selectName = $control_name . '[' . $name . '][day]';
            $selectId = $control_name . '-' . $name . '-day';
            $html[] = \Hubzero\Facades\Html::select(
                'genericlist',
                $options,
                $selectName,
                'class="option"',
                'value',
                'text',
                $day,
                $selectId
            );
        }

        if ($c > 1) {
            $html[] = '</fieldset>';
        }
        return '<span class="field-wrap">' . implode("\n", $html) . '</span>';
    }

    /**
     * Return month text based on numerical value (1-12)
     *
     * @param   integer  $month  Month numerical value
     * @return  string
     */
    private function getMonth($month)
    {
        switch ($month) {
            case 1:
                $monthname = Lang::txt('January');
                break;
            case 2:
                $monthname = Lang::txt('February');
                break;
            case 3:
                $monthname = Lang::txt('March');
                break;
            case 4:
                $monthname = Lang::txt('April');
                break;
            case 5:
                $monthname = Lang::txt('May');
                break;
            case 6:
                $monthname = Lang::txt('June');
                break;
            case 7:
                $monthname = Lang::txt('July');
                break;
            case 8:
                $monthname = Lang::txt('August');
                break;
            case 9:
                $monthname = Lang::txt('September');
                break;
            case 10:
                $monthname = Lang::txt('October');
                break;
            case 11:
                $monthname = Lang::txt('November');
                break;
            case 12:
                $monthname = Lang::txt('December');
                break;
            default:
                $monthname = $month;
                break;
        }
        return $monthname;
    }

    /**
     * Return a value from tag wrappers
     *
     * @param   string  $tag   Wrapper tags to match
     * @param   string  $text  Data
     * @return  string
     */
    private function getValue($tag, $text)
    {
        $pattern = "/<$tag>(.*?)<\/$tag>/i";
        preg_match($pattern, $text, $matches);
        return (isset($matches[1]) ? $matches[1] : '');
    }

    /**
     * Display a value
     *
     * @param   string  $value  Data
     * @return  string  Formatted string.
     */
    public function display($value)
    {
        $year  = intval($this->getValue('year', $value));
        $month = intval($this->getValue('month', $value));
        $day   = intval($this->getValue('day', $value));

        $html = '';
        if ($day && $day != 0) {
            $html .= $day . ' ';
        }
        if ($month && $month != 0) {
            $html .= $this->getMonth($month) . ' ';
        }
        if ($year && $year != 0) {
            $html .= $year;
        }
        return $html;
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

        if (!isset($element->options)) {
            $element->options = array();
        }

        if (count($element->options) < 1) {
            $opt = new stdClass();
            $opt->label = '1950';
            $opt->value = '1950';

            $element->options[] = $opt;
        }
        if (count($element->options) < 2) {
            $opt = new stdClass();
            $opt->label = '';
            $opt->value = '';

            $element->options[] = $opt;
        }

        $k = 0;

        $html[] = '<table class="admintable" id="' . $name . '">';
        $html[] = '<tbody>';
        $html[] = '<tr>';
        $labelFor = $control_name . '-' . $name . '-year';
        $html[] = '<td><label for="' . $labelFor . '">' . Lang::txt('Year') . '</label></td>';
        $inputName = $control_name . '[' . $name . '][year]';
        $checked = isset($element->year) && $element->year == 1 ? 'checked="checked"' : '';
        $html[] = '<td><input type="checkbox" name="' . $inputName . '" id="' . $labelFor
            . '" value="1" ' . $checked . ' /></td>';
        if (isset($element->options) && is_array($element->options)) {
            foreach ($element->options as $option) {
                $labelId = $control_name . '-' . $name . '-label-' . $k;
                $labelText = $k == 0 ? Lang::txt('Start') : Lang::txt('End');
                $html[] = '<td><label for="' . $labelId . '">' . $labelText . '</label></td>';
                $optName = $control_name . '[' . $name . '][options][' . $k . '][label]';
                $optValue = $k == 0 ? ($option->label ? $option->label : 1950) : $option->label;
                $html[] = '<td><input type="text" size="4" name="' . $optName . '" id="'
                    . $labelId . '" value="' . $optValue . '" /></td>';

                $k++;
            }
        }
        $html[] = '</tr>';
        $html[] = '<tr>';
        $labelFor = $control_name . '-' . $name . '-month';
        $html[] = '<td><label for="' . $labelFor . '">' . Lang::txt('Month') . '</label></td>';
        $inputName = $control_name . '[' . $name . '][month]';
        $checked = isset($element->month) && $element->month == 1 ? 'checked="checked"' : '';
        $html[] = '<td colspan="3"><input type="checkbox" name="' . $inputName . '" id="'
            . $labelFor . '" value="1" ' . $checked . ' /></td>';
        $html[] = '</tr>';
        $html[] = '<tr>';
        $labelFor = $control_name . '-' . $name . '-day';
        $html[] = '<td><label for="' . $labelFor . '">' . Lang::txt('Day') . '</label></td>';
        $inputName = $control_name . '[' . $name . '][day]';
        $checked = isset($element->day) && $element->day == 1 ? 'checked="checked"' : '';
        $html[] = '<td colspan="3"><input type="checkbox" name="' . $inputName . '" id="'
            . $labelFor . '" value="1" ' . $checked . ' /></td>';
        $html[] = '</tr>';
        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode("\n", $html);
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
