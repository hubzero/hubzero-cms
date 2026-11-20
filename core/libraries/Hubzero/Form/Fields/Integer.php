<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Select as Dropdown;

/**
 * Provides a select list of integers with specified first, last and step values.
 */
class Integer extends Select
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'Integer';

    /**
     * Method to get the field options.
     *
     * @return  array<mixed>  The field option objects.
     */
    protected function getOptions()
    {
        $element = $this->fieldElement();

        // Initialize variables.
        $options = [];

        // Initialize some field attributes.
        $first = (int) $element['first'];
        $last  = (int) $element['last'];
        $step  = (int) $element['step'];

        // Sanity checks.
        if ($step == 0) {
            // Step of 0 will create an endless loop.
            return $options;
        } elseif ($first < $last && $step < 0) {
            // A negative step will never reach the last number.
            return $options;
        } elseif ($first > $last && $step > 0) {
            // A position step will never reach the last number.
            return $options;
        }

        // Build the options array.
        for ($i = $first; $i <= $last; $i += $step) {
            $options[] = Dropdown::option($i);
        }

        // Merge any additional options in the XML definition.
        return array_merge(parent::getOptions(), $options);
    }
}
