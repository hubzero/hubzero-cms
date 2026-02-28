<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Framework\Config;

/**
 * Simple key-value registry for component parameters and state.
 */
class Registry
{
    private array $data = [];

    public function __construct(array|string|null $data = null)
    {
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            $this->data = is_array($decoded) ? $decoded : [];
        } elseif (is_array($data)) {
            $this->data = $data;
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        // Support dot notation
        if (str_contains($key, '.')) {
            $segments = explode('.', $key);
            $value = $this->data;

            foreach ($segments as $segment) {
                if (!is_array($value) || !array_key_exists($segment, $value)) {
                    return $default;
                }
                $value = $value[$segment];
            }

            // Match legacy behavior: treat null and empty string as "not set"
            return ($value !== null && $value !== '') ? $value : $default;
        }

        if (!array_key_exists($key, $this->data)) {
            return $default;
        }

        $value = $this->data[$key];

        // Match legacy behavior: treat null and empty string as "not set"
        return ($value !== null && $value !== '') ? $value : $default;
    }

    public function set(string $key, mixed $value): static
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Set a default value — uses get() to resolve (which treats empty/null
     * as "not set"), then stores the resolved value. Matches the legacy
     * Hubzero\Config\Registry::def() behavior.
     */
    public function def(string $key, mixed $value = ''): mixed
    {
        $resolved = $this->get($key, (string) $value);
        $this->set($key, $resolved);
        return $resolved;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Merge another Registry or array into this one.
     */
    public function merge(mixed $source): static
    {
        if (is_array($source)) {
            $data = $source;
        } elseif ($source instanceof self) {
            $data = $source->toArray();
        } elseif (method_exists($source, 'toArray')) {
            $data = $source->toArray();
        } else {
            $data = (array) $source;
        }
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Parse an INI-format string into the registry.
     */
    public function parse(string $data): static
    {
        $parsed = parse_ini_string($data);
        if (is_array($parsed)) {
            $this->data = array_merge($this->data, $parsed);
        }
        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function __toString(): string
    {
        return json_encode($this->data);
    }
}
