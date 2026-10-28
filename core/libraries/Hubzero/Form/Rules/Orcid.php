<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Rules;

use Hubzero\Form\Rule;

/**
 * Form Rule class for ORCID iDs (0000-0002-1825-0097)
 */
class Orcid extends Rule
{
    /**
     * Method to test for a well-formed ORCID iD with a valid check digit
     *
     * @param   object   &$element  The SimpleXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed    $value     The form field value to validate.
     * @param   string   $group     The field name group control value.
     * @param   object   &$input    An optional Registry object with the entire data set to validate against the entire form.
     * @param   object   &$form     The form object for which the field is being tested.
     * @return  boolean  True if the value is valid, false otherwise.
     */
    public function test(&$element, $value, $group = null, &$input = null, &$form = null)
    {
        // If the field is empty and not required, the field is valid.
        $required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');
        if (!$required && empty($value)) {
            return true;
        }

        $value = strtoupper(trim((string) $value));

        if (!preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/', $value)) {
            return false;
        }

        // ISO 7064 11,2 check digit over the first 15 digits
        $digits = str_replace('-', '', $value);
        $total  = 0;
        for ($i = 0; $i < 15; $i++) {
            $total = ($total + (int) $digits[$i]) * 2;
        }
        $check = (12 - ($total % 11)) % 11;

        return $digits[15] === ($check == 10 ? 'X' : (string) $check);
    }
}
