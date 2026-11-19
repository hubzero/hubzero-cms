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
        $element = $this->fieldElement();

        // Initialize some field attributes.
        $size      = $element['size'] ? ' size="' . (int) $element['size'] . '"' : '';
        $maxLength = $element['maxlength'] ? ' maxlength="' . (int) $element['maxlength'] . '"' : '';
        $class     = $element['class'] ? ' class="' . (string) $element['class'] . '"' : '';
        $auto      = ((string) $element['autocomplete'] == 'off') ? ' autocomplete="off"' : '';
        $readonly  = ((string) $element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $disabled  = ((string) $element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $meter     = ((string) $element['strengthmeter'] == 'true');
        $threshold = $element['threshold'] ? (int) $element['threshold'] : 66;

        $script = '';
        if ($meter) {
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

        return '<input type="password" name="' . $this->name . '" id="' . $this->id . '"'
            . ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"'
            . $auto . $class . $readonly . $disabled . $size . $maxLength . ' autocomplete="off" />' . $script;
    }
}
