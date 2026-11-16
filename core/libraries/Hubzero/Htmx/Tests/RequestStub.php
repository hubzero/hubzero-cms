<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Htmx\Tests;

class RequestStub
{
    /**
     * @var  array<string, string>
     */
    private array $headers = [];
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
     * @param   array<string, mixed>  $server
     * @param   array<string, mixed>  $query
     */
    public function __construct(array $headers = [], array $server = [], array $query = [])
    {
        foreach ($headers as $key => $value) {
            $this->headers[strtolower((string) $key)] = (string) $value;
        }
        $this->server = $server;
        $this->query = $query;
    }

    public function header(string $key, string $default = ''): string
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    /**
     * @param   mixed  $default
     * @return  mixed
     */
    public function server(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * @param   mixed  $default
     * @return  mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }
}
