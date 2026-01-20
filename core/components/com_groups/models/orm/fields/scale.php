<?php

// phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Fields\Radio;
use Lang;

/**
 * Supports a scaled selection field
 */
class Scale extends Radio
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'scale';

    /**
     * Method to get the field options for radio buttons.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        // Initialize variables.
        $options = array();

        for ($i = 1; $i < 6; $i++) {
            $option = new \stdClass();
            $option->value = $i;
            $option->text  = $i;

            $options[] = $option;
        }

        return $options;
    }

    /**
     * Method to get the radio button field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        // Initialize variables.
        $html = array();

        // Initialize some field attributes.
        if ($this->element['class']) {
            $class = ' class="radio scale ' . (string) $this->element['class'] . '"';
        } else {
            $class = ' class="radio scale"';
        }

        // Start the radio field output.
        $html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

        // Get the field options.
        $options = $this->getOptions();

        $percent = count($options) ? (100 / count($options)) : 0;

        $found = false;

        \Document::addStyleDeclaration('
			#' . $this->id . ' .li-' . $this->id . ' {
				width: ' . $percent . '%;
			}
		');

        $html[] = '<ul>';

        // Build the radio field output.
        foreach ($options as $i => $option) {
            // Initialize some option attributes.
            $checked  = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
            $class    = !empty($option->class) ? ' class="' . $option->class . '"' : '';
            $disabled = !empty($option->disable) ? ' disabled="disabled"' : '';

            if ($checked) {
                $found = true;
            }

            // Initialize some JavaScript option attributes.
            $onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

            $html[] = '<li class="li-' . $this->id . '">';
                $html[] = '<div class="input-wrap">';
                    $inputId = $this->id . $i;
                    $escapedValue = htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8');
                    $html[] = '<input type="radio" id="' . $inputId . '" name="' . $this->name
                        . '" value="' . $escapedValue . '"'
                        . $checked . $class . $onclick . $disabled . '/>';
                    $labelText = Lang::alt(
                        $option->text,
                        preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)
                    );
                    $html[] = '<label for="' . $inputId . '"' . $class . '>' . $labelText . '</label>';
                $html[] = '</div>';
            $html[] = '</li>';
        }

        $html[] = '</ul>';

        // End the radio field output.
        $html[] = '</fieldset>';

        return implode($html);
    }

    /**
     * Render the supplied value
     *
     * @param   string  $value
     * @return  string
     */
    public function renderValue($value)
    {
        $options = $this->getOptions();
        $top = count($options);
        $top = $top ?: 1;

        $percent = (intval($value) / $top) * 100;

        $cls = 'low';
        if ($percent > 30) {
            $cls = 'med';
        }
        if ($percent > 60) {
            $cls = 'hi';
        }

        \Document::addStyleDeclaration('
			.graph .bar' . $this->id . ' {
				width: ' . $percent . '%;
			}
		');

        $html = array();
        $html[] = '<div class="graph">';
        $barClass = 'bar bar' . $this->id . ' ' . $cls;
        $barText = Lang::txt('%s out of %s', $value, $top);
        $html[] = '<strong class="' . $barClass . '"><span>' . $barText . '</span></strong>';
        $html[] = '</div>';

        $value = implode("\n", $html);

        return $value;
    }
}
