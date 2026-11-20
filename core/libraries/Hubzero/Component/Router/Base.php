<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Router;

/**
 * Base component routing class
 */
abstract class Base implements RouterInterface
{
    /**
     * Generic method to preprocess a URL
     *
     * @param   array<string, mixed>  $query  An associative array of URL arguments
     * @return  array<string, mixed>  The URL arguments to use to assemble the subsequent URL.
     */
    public function preprocess($query)
    {
        return $query;
    }
}
