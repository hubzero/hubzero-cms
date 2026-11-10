<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class DebugSmokeConfigStub
{
    private array $values;

    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    public function get(string $key, $default = null)
    {
        return $this->values[$key] ?? $default;
    }
}
