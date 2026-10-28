<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
        // Initialize some field attributes.
        $baseClass = 'paragraph paragraph-' . $this->id;
        if ($this->element['class']) {
            $class = ' class="' . $baseClass . ' ' . (string) $this->element['class'] . '"';
        } else {
            $class = ' class="' . $baseClass . '"';
        }

        // Initialize variables.
        $html = array();

        // Start the radio field output.
        $html[] = '<p id="' . $this->id . '"' . $class . '>';

        $html[] = isset($this->element['description']) ? $this->element['description'] : '';

        // End the radio field output.
        $html[] = '</p>';

        return implode($html);
    }
}
