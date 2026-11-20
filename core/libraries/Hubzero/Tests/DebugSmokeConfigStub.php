<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class DebugSmokeConfigStub
{
    /**
     * @var  array<string, mixed>
     */
    private array $values;

    /**
     * @param   array<string, mixed>  $values
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * @param   mixed  $default
     * @return  mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->values[$key] ?? $default;
    }
}
