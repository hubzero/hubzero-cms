<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug;

/**
 * Shared helpers for debug/profile header encoding and emission.
 */
class HeaderSupport
{
    /**
     * Encode payload as JSON with fallback and optional truncation.
     *
     * @param   mixed   $payload
     * @param   string  $fallback
     * @param   int     $maxLength
     * @return  string
     */
    public static function encodeJson($payload, string $fallback, int $maxLength = 0): string
    {
        $encoded = json_encode($payload);
        if (!is_string($encoded)) {
            $encoded = $fallback;
        }

        if ($maxLength > 0 && strlen($encoded) > $maxLength) {
            $encoded = substr($encoded, 0, $maxLength - 3) . '...';
        }

        return $encoded;
    }

    /**
     * Emit response header via App response object when available.
     *
     * @param   string  $name
     * @param   string  $value
     * @return  void
     */
    public static function emitHeader(string $name, string $value): void
    {
        $response = \App::get('response');
        if ($response && isset($response->headers)) {
            $response->headers->set($name, $value);
            return;
        }

        header($name . ': ' . $value, true);
    }

    /**
     * @return  bool
     */
    public static function isDebugLogEnabled(): bool
    {
        $config = \App::get('config');
        if ($config && method_exists($config, 'get')) {
            return (bool) $config->get('debug');
        }

        return false;
    }

    /**
     * @param   string  $channel
     * @param   string  $encodedPayload
     * @return  void
     */
    public static function logProfile(string $channel, string $encodedPayload): void
    {
        if (!self::isDebugLogEnabled()) {
            return;
        }

        error_log('[hubzero:' . trim($channel) . ':profile] ' . $encodedPayload);
    }
}
