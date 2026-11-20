<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Inertia\Tests;

class RequestStub
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
     * @param   array<string, mixed>  $server
     * @param   array<string, mixed>  $query
     */
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
