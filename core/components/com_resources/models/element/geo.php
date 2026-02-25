<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Element;

use Components\Resources\Models\Element as Base;
use Hubzero\Facades\Document;
use Hubzero\Facades\Lang;

/**
 * Renders a geolocation element
 */
class Geo extends Base
{
    /**
     * Element name
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name = 'Geo Location';

    /**
     * Flag for if JS has been pushed to document or not
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_script = false;

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
        $labelId = $control_name . '-' . $name . '-lbl';
        $labelFor = $control_name . '-' . $name;
        $output = '<label id="' . $labelId . '" for="' . $labelFor . '"';
        if ($description) {
            $output .= ' class="hasTip" title="' . Lang::txt($label) . '::'
                . Lang::txt($description) . '">';
        } else {
            $output .= '>';
        }
        $hintText = Lang::txt('(street, city, state/province postal-code, country)');
        $output .= Lang::txt($label) . ' <span class="hint">' . $hintText . '</span>';
        $isRequired = isset($element->required) && $element->required;
        $requiredSpan = ' <span class="required">' . Lang::txt('JOPTION_REQUIRED') . '</span>';
        $output .= $isRequired ? $requiredSpan : '';
        $output .= '</label>';

        return $output;
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
        if (!$this->_script) {
            Document::addScript('//maps.google.com/maps/api/js?sensor=false');
            $geoJs = '/core/components/com_resources/models/element/assets/js/geo.js';
            Document::addScript(\Hubzero\Facades\Request::base(true) . $geoJs);
            $this->_script = true;
        }

        $size  = (isset($element->size)  ? 'size="' . $element->size . '"' : '');
        $class = (isset($element->class) ? 'class="geolocation ' . $element->class . '"' : 'class="geolocation"');

        $address = $this->getValue('value', $value);
        $lat = $this->getValue('lat', $value);
        $lat = (trim($lat)) ? $lat : '0.0';
        $lng = $this->getValue('lng', $value);
        $lng = (trim($lng)) ? $lng : '0.0';

        $value = preg_replace('/<lat>(.*?)<\/lat>/i', '', $value);
        $value = preg_replace('/<lng>(.*?)<\/lng>/i', '', $value);

        // Required to avoid a cycle of encoding &
        // html_entity_decode was used in place of htmlspecialchars_decode because
        // htmlspecialchars_decode is not compatible with PHP 4
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

        $inputName = $control_name . '[' . $name . '][value]';
        $inputId = $control_name . '-' . $name;
        $html  = '<input type="text" name="' . $inputName . '" id="' . $inputId
            . '" value="' . $address . '" ' . $class . ' ' . $size . ' />';
        $latName = $control_name . '[' . $name . '][lat]';
        $html .= '<input type="hidden" name="' . $latName . '" id="' . $inputId
            . '-lat" value="' . $lat . '" />';
        $lngName = $control_name . '[' . $name . '][lng]';
        $html .= '<input type="hidden" name="' . $lngName . '" id="' . $inputId
            . '-lng" value="' . $lng . '" />';

        return $html;
    }

    /**
     * Return a value from tag wrappers
     *
     * @param   string  $tag  Wrapper tags to match
     * @param   string  $text Data
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
     * @param   string  $value   Data
     * @return  string  Formatted string.
     */
    public function display($value)
    {
        return trim($this->getValue('value', $value));
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
