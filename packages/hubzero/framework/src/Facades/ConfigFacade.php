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
}
