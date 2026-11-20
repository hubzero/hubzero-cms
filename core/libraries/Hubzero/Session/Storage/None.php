<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Session\Storage;

use Hubzero\Session\Store;

/**
 * Session handler for 'None'
 */
class None extends Store
{
    /**
     * Register the functions of this class with PHP's session handler
     *
     * @param   array<mixed>  $options  Optional parameters.
     * @return  void
     */
    public function register($options = [])
    {
        // Let php handle the session storage
    }
}
