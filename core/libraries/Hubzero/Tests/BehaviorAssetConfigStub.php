<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class BehaviorAssetConfigStub
{
    public function get(string $key, $default = null)
    {
        if ($key === 'debug') {
            return false;
        }

        return $default;
    }
}
