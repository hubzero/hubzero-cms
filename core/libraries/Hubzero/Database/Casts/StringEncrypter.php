<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Casts;

/**
 * Interface for string encryption services
 *
 * Any encryption implementation can be used with AsEncryptedString
 * by implementing this interface.
 */
interface StringEncrypter
{
    /**
     * Encrypt the given value
     *
     * @param  string  $value
     * @return string
     */
    public function encrypt(string $value): string;

    /**
     * Decrypt the given value
     *
     * @param  string  $value
     * @return string
     */
    public function decrypt(string $value): string;
}
