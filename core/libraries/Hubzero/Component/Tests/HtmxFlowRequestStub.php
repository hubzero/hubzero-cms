<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

class HtmxFlowRequestStub
{
    public array $headers = [];

    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    public function header(string $name, $default = '')
    {
        foreach ($this->headers as $key => $value) {
            if (strtolower((string) $key) === strtolower($name)) {
                return $value;
            }
        }

        return $default;
    }
}
