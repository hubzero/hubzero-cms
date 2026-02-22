<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug;

/**
 * Shared helpers for preserving protocol debug mode across requests/forms.
 */
class ModeSupport
{
    /**
     * Preserve debug mode param in outbound params when debug is enabled.
     *
     * @param   bool    $enabled
     * @param   array   $params
     * @param   string  $name
     * @param   string  $value
     * @return  array
     */
    public static function preserveParam(bool $enabled, array $params, string $name, string $value): array
    {
        if (!$enabled) {
            return $params;
        }

        $key = self::sanitizeFieldName($name);
        if ($key === '') {
            return $params;
        }

        $params[$key] = $value;

        return $params;
    }

    /**
     * Render hidden input for preserving debug mode across form submits.
     *
     * @param   bool    $enabled
     * @param   string  $name
     * @param   string  $value
     * @return  string
     */
    public static function renderHiddenInput(bool $enabled, string $name, string $value): string
    {
        $params = self::preserveParam($enabled, array(), $name, $value);
        if (empty($params)) {
            return '';
        }

        $key = (string) key($params);
        $val = (string) current($params);

        return sprintf(
            '<input type="hidden" name="%s" value="%s" />',
            htmlspecialchars($key, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($val, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * @param   string  $name
     * @return  string
     */
    public static function sanitizeFieldName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_\\-\\[\\]]/', '', $name) ?? '';
    }
}
