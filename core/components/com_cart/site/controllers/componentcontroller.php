<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site\Controllers;

// Imported: unqualified, Request resolves to this namespace's own Request
// controller (request.php) and getParams() dies on "undefined method getString()"
use Hubzero\Facades\Request;

/**
 *  Base component controller class
 */
class ComponentController extends \Hubzero\Component\SiteController
{
    /**
     * Parse the URL parameters and map each parameter (in order) to the given array of names
     *
     * @param       mixed $varNames (array of strings or string): Array of names anr single name to map the URL
     *                              parameter(s) to
     * @return      object  Object with properties named after var names mapped to URL parameters
     */
    protected function getParams($varNames)
    {
        $i = 0;

        if (!is_array($varNames)) {
            $varNames = [$varNames];
        }

        $params = new \stdClass();

        // Go through each var name and assign a sequential URL parameter's value to it
        foreach ($varNames as $varName) {
            $value = Request::getString('p' . $i, '');
            if (!empty($value)) {
                $params->$varName = $value;
            } else {
                break;
            }
            $i++;
        }
        return $params;
    }
}
