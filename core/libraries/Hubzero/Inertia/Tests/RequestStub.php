<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Inertia\Tests;

class RequestStub
{
    private array $headers = [];
    private string $method = 'GET';
    private array $server = [];
    private array $query = [];

    public function __construct(array $headers = [], string $method = 'GET', array $server = [], array $query = [])
    {
        foreach ($headers as $key => $value) {
            $this->headers[strtolower((string) $key)] = (string) $value;
        }

        $this->method = strtoupper($method);
        $this->server = $server;
        $this->query = $query;
    }

    public function header(string $key, string $default = ''): string
    {
        $normalized = strtolower($key);
        return $this->headers[$normalized] ?? $default;
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
