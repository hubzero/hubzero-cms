<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

class InertiaFlowRequestStub
{
    public array $headers = [];
    public string $method = 'GET';
    public array $server = [];
    public array $query = [];

    public function __construct(array $headers = [], string $method = 'GET', array $server = [], array $query = [])
    {
        $this->headers = $headers;
        $this->method = $method;
        $this->server = $server;
        $this->query = $query;
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

    public function getMethod(): string
    {
        return $this->method;
    }

    public function server(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    public function get(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }
}
