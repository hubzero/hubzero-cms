<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Site\Controllers;

use Hubzero\Facades\Request;

/**
 *  Base component controller class
 */
class ComponentController extends \Hubzero\Component\SiteController
{
    /**
     * Parse the URL parameters and map each parameter (in order) to the given array of names
     *
     * @param   array<mixed>   $varNames  Array of names to map the URL parameters to
     * @return  \stdClass  Object with properties named after var names mapped to URL parameters
     */
    protected function getParams($varNames)
    {
        $i = 0;
        // Assigning to false was a fatal error once a parameter was given
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
