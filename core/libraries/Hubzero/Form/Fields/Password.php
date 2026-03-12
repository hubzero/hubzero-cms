<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;
use Hubzero\Html\Builder\Asset;

/**
 * Text field for passwords
 */
class Password extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Password';

    /**
     * Method to get the field input markup for password.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        // Initialize some field attributes.
        $size      = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
        $maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
        $isDaisyui = \Hubzero\Facades\Document::getCssFramework() === 'daisyui';
        $xmlClass  = $this->element['class'] ? (string) $this->element['class'] : '';
        if ($isDaisyui) {
            $xmlClass = trim(str_replace('inputbox', '', $xmlClass));
            $cls      = trim('input input-bordered input-sm w-full' . ($xmlClass ? ' ' . $xmlClass : ''));
        } else {
            $cls = $xmlClass;
        }
        $class = $cls ? ' class="' . $cls . '"' : '';
        $auto      = ((string) $this->element['autocomplete'] == 'off') ? ' autocomplete="off"' : '';
        $readonly  = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $disabled  = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $meter     = ((string) $this->element['strengthmeter'] == 'true');
        $threshold = $this->element['threshold'] ? (int) $this->element['threshold'] : 66;

        $script = '';
        $meterAttr = '';
        if ($meter) {
            if ($isDaisyui) {
                // CSP-safe: admin.js initPasswordStrength() picks up this data attribute
                $meterAttr = ' data-strength-meter="' . $threshold . '"';
            } else {
                Asset::script('system/passwordstrength.js', true, true);
                $script = '<script type="text/javascript">jQuery(document).ready(function ($) {
				$("#' . $this->id . '").passwordstrength({
					threshold: ' . $threshold . ',
					onUpdate: function(element, strength, threshold) {
						element.set("data-passwordstrength", strength);
					}
				});
			});</script>';
            }
        }

        return '<input type="password" name="' . $this->name . '" id="' . $this->id . '"' .
            ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
            $auto . $class . $readonly . $disabled . $size . $maxLength . $meterAttr .
            ' autocomplete="off" />' . $script;
    }
}
