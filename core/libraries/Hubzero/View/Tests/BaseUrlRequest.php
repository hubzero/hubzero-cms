<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
