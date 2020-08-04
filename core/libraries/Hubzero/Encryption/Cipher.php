<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Encryption;

/**
 * Cipher interface.
 *
 * Inspired by Joomla's JCryptCipher class
 */
interface Cipher
{
	/**
	 * Method to decrypt a data string.
	 *
	 * @param   string  $data  The encrypted string to decrypt.
	 * @param   object  $key   The key[/pair] object to use for decryption.
	 * @return  string  The decrypted data string.
	 */
	public function decrypt($data, Key $key);

	/**
	 * Method to encrypt a data string.
	 *
	 * @param   string  $data  The data string to encrypt.
	 * @param   object  $key   The key[/pair] object to use for encryption.
	 * @return  string  The encrypted data string.
	 */
	public function encrypt($data, Key $key);

	/**
	 * Method to generate a new encryption key[/pair] object.
	 *
	 * @param   array   $options  Key generation options.
	 * @return  object
	 */
	public function generateKey(array $options = array());
}
