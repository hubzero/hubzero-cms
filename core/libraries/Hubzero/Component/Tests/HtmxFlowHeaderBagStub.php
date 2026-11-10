<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

class HtmxFlowHeaderBagStub
{
    public array $headers = [];

    public function set(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function get(string $name, $default = '')
    {
        return $this->headers[$name] ?? $default;
    }
}
