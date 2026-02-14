<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Casts;

use Hubzero\Database\Relational;
use Hubzero\Encryption\Encrypter;

/**
 * Cast attribute to/from encrypted string
 *
 * Requires the Hubzero Encryption service to be available.
 *
 * Usage:
 * ```php
 * protected $casts = [
 *     'secret_key' => AsEncryptedString::class,
 * ];
 * ```
 */
class AsEncryptedString implements CastsAttributes
{
    /**
     * Decrypt when reading
     *
     * @param  Relational  $model
     * @param  string      $key
     * @param  mixed       $value
     * @param  array       $attributes
     * @return string|null
     */
    public function get(Relational $model, string $key, $value, array $attributes)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $encrypter = $this->getEncrypter();

        if ($encrypter === null) {
            return $value;
        }

        try {
            return $encrypter->decrypt($value);
        } catch (\Exception $e) {
            // Return raw value if decryption fails (might not be encrypted)
            return $value;
        }
    }

    /**
     * Encrypt when storing
     *
     * @param  Relational  $model
     * @param  string      $key
     * @param  mixed       $value
     * @param  array       $attributes
     * @return string|null
     */
    public function set(Relational $model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        $encrypter = $this->getEncrypter();

        if ($encrypter === null) {
            return $value;
        }

        return $encrypter->encrypt($value);
    }

    /**
     * Get the encrypter instance
     *
     * @return Encrypter|null
     */
    protected function getEncrypter()
    {
        // Try to get from app container
        if (function_exists('app') && ($app = app()) !== null && $app->has('encrypter')) {
            return $app->get('encrypter');
        }

        // Try to get from App facade
        if (class_exists('App') && method_exists('App', 'get')) {
            try {
                return \App::get('encrypter');
            } catch (\Exception $e) {
                // Encrypter not available
            }
        }

        return null;
    }
}
