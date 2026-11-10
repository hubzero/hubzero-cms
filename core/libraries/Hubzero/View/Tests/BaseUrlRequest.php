<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Tests;

/**
 * A request that knows only the site's base URL
 */
class BaseUrlRequest
{
    /**
     * @param   bool    $pathOnly
     * @return  string
     */
    public function base($pathOnly = false)
    {
        return '';
    }
}
