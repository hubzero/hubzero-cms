<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

class HtmxFlowRequestStub
{
    /**
     * @var  array<string, string>
     */
    public array $headers = [];

    /**
     * @param   array<string, mixed>  $headers
     */
    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    /**
     * @param   string  $default
     * @return  mixed
     */
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
