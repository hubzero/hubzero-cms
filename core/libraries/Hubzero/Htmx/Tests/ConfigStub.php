<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Htmx\Tests;

class ConfigStub
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
