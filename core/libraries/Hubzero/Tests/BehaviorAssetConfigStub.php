<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class BehaviorAssetConfigStub
{
    /**
     * @param   mixed  $default
     * @return  mixed
     */
    public function get(string $key, $default = null)
    {
        if ($key === 'debug') {
            return false;
        }

        return $default;
    }
}
