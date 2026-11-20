<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

class HtmxFlowHeaderBagStub
{
    /**
     * @var  array<string, string>
     */
    public array $headers = [];

    public function set(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * @param   string  $default
     * @return  mixed
     */
    public function get(string $name, $default = '')
    {
        return $this->headers[$name] ?? $default;
    }
}
