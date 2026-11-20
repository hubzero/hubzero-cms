<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Supports paragraphs of text
 */
class Paragraph extends Field
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Paragraph';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $element = $this->fieldElement();

        // Initialize some field attributes.
        $baseClass = 'paragraph paragraph-' . $this->id;
        if ($element['class']) {
            $class = ' class="' . $baseClass . ' ' . (string) $element['class'] . '"';
        } else {
            $class = ' class="' . $baseClass . '"';
        }

        // Initialize variables.
        $html = [];

        // Start the radio field output.
        $html[] = '<p id="' . $this->id . '"' . $class . '>';

        $html[] = isset($element['description']) ? $element['description'] : '';

        // End the radio field output.
        $html[] = '</p>';

        return implode($html);
    }
}
