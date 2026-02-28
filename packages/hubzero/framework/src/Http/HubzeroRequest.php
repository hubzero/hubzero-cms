<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Http;

use Illuminate\Http\Request as LaravelRequest;

/**
 * Wraps Laravel's Request with HubZero's typed accessor API.
 *
 * Legacy components use Request::getCmd(), Request::getInt(), etc.
 * This adapter delegates to Laravel's request while providing the
 * same filtering/casting that HubZero components expect.
 */
class HubzeroRequest
{
    private LaravelRequest $request;

    public function __construct(LaravelRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get a variable from the request.
     */
    public function getVar(
        string $key,
        mixed $default = null,
        string $hash = 'input',
        string $type = 'none',
        int $mask = 0
    ): mixed {
        $value = $this->readFromHash($key, $default, $hash);

        return match ($type) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'bool', 'boolean' => (bool) $value,
            'string' => (string) ($value ?? ''),
            'array' => (array) $value,
            'word' => preg_replace('/[^A-Z_]/i', '', (string) $value),
            'cmd' => preg_replace('/[^A-Z0-9_\.-]/i', '', ltrim((string) $value, '.')),
            default => $value,
        };
    }

    /**
     * Set a variable on the request.
     */
    public function setVar(string $name, mixed $value = null, string $hash = 'method', bool $overwrite = true): void
    {
        if ($overwrite || !$this->request->has($name)) {
            $this->request->merge([$name => $value]);
        }
    }

    /**
     * Get a command-safe string (alphanumeric, underscore, dot, hyphen).
     */
    public function getCmd(?string $key = null, mixed $default = null, string $hash = 'input'): string
    {
        $value = (string) $this->readFromHash($key, $default, $hash);
        return preg_replace('/[^A-Z0-9_\.-]/i', '', ltrim($value, '.'));
    }

    /**
     * Get an integer value.
     */
    public function getInt(string $key, int $default = 0, string $hash = 'input'): int
    {
        return (int) $this->readFromHash($key, $default, $hash);
    }

    /**
     * Get an unsigned integer value.
     */
    public function getUInt(string $key, int $default = 0, string $hash = 'input'): int
    {
        return abs((int) $this->readFromHash($key, $default, $hash));
    }

    /**
     * Get a float value.
     */
    public function getFloat(string $key, float $default = 0.0, string $hash = 'input'): float
    {
        return (float) $this->readFromHash($key, $default, $hash);
    }

    /**
     * Get a boolean value.
     */
    public function getBool(?string $key = null, ?bool $default = null, string $hash = 'input'): bool
    {
        return (bool) $this->readFromHash($key, $default, $hash);
    }

    /**
     * Get a word (alpha + underscores only).
     */
    public function getWord(string $key, mixed $default = null, string $hash = 'input'): string
    {
        $value = (string) $this->readFromHash($key, $default, $hash);
        return preg_replace('/[^A-Z_]/i', '', $value);
    }

    /**
     * Get a string value.
     */
    public function getString(string $key, ?string $default = null, string $hash = 'input'): string
    {
        $value = $this->readFromHash($key, $default, $hash);
        return (string) ($value ?? '');
    }

    /**
     * Get an array value.
     */
    public function getArray(?string $key = null, array $default = [], string $hash = 'input'): array
    {
        $value = $this->readFromHash($key, $default, $hash);
        return is_array($value) ? $value : $default;
    }

    public function method(): string
    {
        return $this->request->method();
    }

    public function root(bool $pathonly = false): string
    {
        return $pathonly ? '' : $this->request->root();
    }

    public function base(bool $pathonly = false): string
    {
        return $pathonly ? $this->request->getBaseUrl() : $this->request->root();
    }

    public function current(bool $query = false): string
    {
        return $query ? $this->request->fullUrl() : $this->request->url();
    }

    public function path(): string
    {
        return $this->request->path();
    }

    public function ip(): string
    {
        return $this->request->ip();
    }

    public function segment(int $index, mixed $default = null): mixed
    {
        return $this->request->segment($index, $default);
    }

    public function segments(): array
    {
        return $this->request->segments();
    }

    public function ajax(): bool
    {
        return $this->request->ajax();
    }

    public function secure(): bool
    {
        return $this->request->secure();
    }

    public function has(mixed $key): bool
    {
        return $this->request->has($key);
    }

    public function input(string $key = null, mixed $default = null): mixed
    {
        return $this->request->input($key, $default);
    }

    /**
     * Get the query string parameters.
     */
    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->request->query();
        }
        return $this->request->query($key, $default);
    }

    public function checkToken(string $method = 'post'): bool
    {
        // CSRF is handled by Laravel's middleware
        return true;
    }

    /**
     * Read a value from the appropriate request source.
     */
    private function readFromHash(?string $key, mixed $default, string $hash): mixed
    {
        if ($key === null) {
            return $default;
        }

        return match (strtolower($hash)) {
            'get', 'query' => $this->request->query($key, $default),
            'post' => $this->request->post($key, $default),
            'server' => $this->request->server($key, $default),
            'cookie' => $this->request->cookie($key, $default),
            'files' => $this->request->file($key),
            default => $this->request->input($key, $default),
        };
    }
}
