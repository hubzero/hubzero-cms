<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Casts;

use Hubzero\Database\Relational;

/**
 * Cast attribute to/from encrypted string
 *
 * Requires a StringEncrypter to be injected via setEncrypter().
 * Without one, values pass through unencrypted.
 *
 * Usage:
 * ```php
 * // Wire up in bootstrap:
 * AsEncryptedString::setEncrypter($myEncrypter);
 *
 * // In model:
 * protected $casts = [
 *     'secret_key' => AsEncryptedString::class,
 * ];
 * ```
 */
class AsEncryptedString implements CastsAttributes
{
    /**
     * The encrypter instance
     *
     * @var StringEncrypter|null
     */
    protected static ?StringEncrypter $encrypter = null;

    /**
     * Set the encrypter instance
     *
     * @param  StringEncrypter|null  $encrypter
     * @return void
     */
    public static function setEncrypter(?StringEncrypter $encrypter): void
    {
        static::$encrypter = $encrypter;
    }

    /**
     * Get the encrypter instance
     *
     * @return StringEncrypter|null
     */
    public static function getEncrypter(): ?StringEncrypter
    {
        return static::$encrypter;
    }

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

        if (static::$encrypter === null) {
            return $value;
        }

        try {
            return static::$encrypter->decrypt($value);
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

        if (static::$encrypter === null) {
            return $value;
        }

        return static::$encrypter->encrypt($value);
    }

    /**
     * Reset static state (for worker mode / testing)
     *
     * @return void
     */
    public static function flush(): void
    {
        static::$encrypter = null;
    }
}
