<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Html\Builder\Behavior;

/**
 * Implements a combo box field.
 */
class Combo extends Select
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'Combo';

    /**
     * Method to get the field input markup for a combo box field.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        // Initialize variables.
        $html = array();
        $attr = '';
        $isDaisyui = \Hubzero\Facades\Document::getCssFramework() === 'daisyui';

        // Initialize some field attributes.
        $xmlClass = $this->element['class'] ? (string) $this->element['class'] : '';
        if ($isDaisyui) {
            $xmlClass = trim(str_replace('inputbox', '', $xmlClass));
            $attr .= ' class="' . trim('input input-bordered input-sm w-full' . ($xmlClass ? ' ' . $xmlClass : '')) . '"';
        } else {
            $attr .= ' class="combobox' . ($xmlClass ? ' ' . $xmlClass : '') . '"';
        }
        $attr .= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

        // Initialize JavaScript field attributes.
        if ($this->element['onchange']) {
            $attr .= $isDaisyui
                ? self::cspDataAttr((string) $this->element['onchange'])
                : ' onchange="' . (string) $this->element['onchange'] . '"';
        }

        // Get the field options.
        $options = $this->getOptions();

        if ($isDaisyui) {
            // Native HTML5 datalist combo box.
            $attr .= ' list="datalist-' . $this->id . '"';

            $html[] = '<input type="text" name="' . $this->name .
                '" id="' . $this->id . '"' .
                ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
                $attr . '/>';

            $html[] = '<datalist id="datalist-' . $this->id . '">';
            foreach ($options as $option) {
                $html[] = '<option value="' . htmlspecialchars($option->text, ENT_COMPAT, 'UTF-8') . '">';
            }
            $html[] = '</datalist>';
        } else {
            // Legacy jQuery combobox behavior.
            Behavior::combobox();

            $html[] = '<input type="text" name="' . $this->name .
                '" id="' . $this->id . '"' .
                ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
                $attr . '/>';

            $html[] = '<ul id="combobox-' . $this->id . '" style="display:none;">';
            foreach ($options as $option) {
                $html[] = '<li>' . $option->text . '</li>';
            }
            $html[] = '</ul>';
        }

        return implode($html);
    }
}
