<?php

namespace Hubzero\Framework\Facades;

/**
 * HubZero Config facade — bridges to Laravel's config.
 *
 * Maps HubZero config keys (sitename, etc.) to Laravel config values.
 */
class ConfigFacade
{
    public static function get(string $key, mixed $default = null): mixed
    {
        // Map common HubZero config keys to Laravel equivalents
        return match ($key) {
            'sitename' => config('hubzero.app.sitename', $default ?? config('app.name', 'Hubzero')),
            'sef' => true,
            'sef_rewrite' => true,
            'debug' => config('app.debug', false),
            'cachetime' => 15,
            default => config('hubzero.app.' . $key, $default),
        };
    }

    public static function toArray(): array
    {
        return config('hubzero', []);
    }

    public static function set(string $key, mixed $value): void
    {
        config(['hubzero.app.' . $key => $value]);
    }

    /**
     * Return the root config object (all HubZero config sections).
     */
    public static function getRoot(): object
    {
        $data = config('hubzero', []);

        return new class($data) {
            private array $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function toArray(): array
            {
                return $this->data;
            }

            public function get(string $key, mixed $default = null): mixed
            {
                return $this->data[$key] ?? $default;
            }
        };
    }
}
