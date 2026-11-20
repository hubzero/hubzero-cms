<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Inertia\Tests;

class HeaderBagStub
{
    /**
     * @var  array<string, string>
     */
    public array $headers = [];

    public function set(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function get(string $name, string $default = ''): string
    {
        return $this->headers[$name] ?? $default;
    }
}
