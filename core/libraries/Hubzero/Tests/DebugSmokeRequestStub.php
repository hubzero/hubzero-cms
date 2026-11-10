<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class DebugSmokeRequestStub
{
    private array $headers = [];
    private string $method;
    private array $query;
    private array $server;

    public function __construct(array $headers = [], string $method = 'GET', array $query = [], array $server = [])
    {
        foreach ($headers as $key => $value) {
            $this->headers[strtolower((string) $key)] = (string) $value;
        }
        $this->method = strtoupper($method);
        $this->query = $query;
        $this->server = $server;
    }

    public function header(string $key, string $default = ''): string
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function get(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function server(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }
}
