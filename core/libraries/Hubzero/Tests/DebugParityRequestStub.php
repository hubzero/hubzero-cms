<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class DebugParityRequestStub
{
    /**
     * @var  array<string, string>
     */
    private array $headers = [];
    private string $method = 'GET';
    /**
     * @var  array<string, mixed>
     */
    private array $server = [];
    /**
     * @var  array<string, mixed>
     */
    private array $query = [];

    /**
     * @param   array<string, mixed>  $headers
     * @param   array<string, mixed>  $query
     * @param   array<string, mixed>  $server
     */
    public function __construct(array $headers = [], array $query = [], array $server = [], string $method = 'GET')
    {
        foreach ($headers as $key => $value) {
            $this->headers[strtolower((string) $key)] = (string) $value;
        }
        $this->query = $query;
        $this->server = $server;
        $this->method = strtoupper($method);
    }

    public function header(string $name, string $default = ''): string
    {
        return $this->headers[strtolower($name)] ?? $default;
    }

    /**
     * @param   mixed  $default
     * @return  mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->query[$name] ?? $default;
    }

    /**
     * @param   mixed  $default
     * @return  mixed
     */
    public function server(string $name, $default = null)
    {
        return $this->server[$name] ?? $default;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
