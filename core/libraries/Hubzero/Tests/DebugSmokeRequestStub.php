<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class DebugSmokeRequestStub
{
    /**
     * @var  array<string, string>
     */
    private array $headers = [];
    private string $method;
    /**
     * @var  array<string, mixed>
     */
    private array $query;
    /**
     * @var  array<string, mixed>
     */
    private array $server;

    /**
     * @param   array<string, mixed>  $headers
     * @param   array<string, mixed>  $query
     * @param   array<string, mixed>  $server
     */
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

    /**
     * @param   mixed  $default
     * @return  mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * @param   mixed  $default
     * @return  mixed
     */
    public function server(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }
}
