<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Encryption\Cipher;

use Hubzero\Encryption\Cipher;
use Hubzero\Encryption\Key;
use InvalidArgumentException;

/**
 * Cipher for Simple encryption, decryption and key generation.
 *
 * Inspired by Joomla's JCryptCipherSimple class
 */
class Simple implements Cipher
{
	/**
	 * Method to decrypt a data string.
	 *
	 * @param   string  $data  The encrypted string to decrypt.
	 * @param   object  $key   The key[/pair] object to use for decryption.
	 * @return  string  The decrypted data string.
	 * @throws  InvalidArgumentException
	 */
	public function decrypt($data, Key $key)
	{
		// Validate key.
		if ($key->type != 'simple')
		{
			throw new InvalidArgumentException('Invalid key of type: ' . $key->type . '.  Expected simple.');
		}

		// Initialise variables.
		$decrypted = '';
		$tmp = $key->public;

		// Convert the HEX input into an array of integers and get the number of characters.
		$chars = $this->_hexToIntArray($data);
		$charCount = count($chars);

		// Repeat the key as many times as necessary to ensure that the key is at least as long as the input.
		for ($i = 0; $i < $charCount; $i = strlen($tmp))
		{
			$tmp = $tmp . $tmp;
		}

		// Get the XOR values between the ASCII values of the input and key characters for all input offsets.
		for ($i = 0; $i < $charCount; $i++)
		{
			$decrypted .= chr($chars[$i] ^ ord($tmp[$i]));
		}

		return $decrypted;
	}

	/**
	 * Method to encrypt a data string.
	 *
	 * @param   string  $data  The data string to encrypt.
	 * @param   object  $key   The key[/pair] object to use for encryption.
	 * @return  string  The encrypted data string.
	 * @throws  InvalidArgumentException
	 */
	public function encrypt($data, Key $key)
	{
		// Validate key.
		if ($key->type != 'simple')
		{
			throw new InvalidArgumentException('Invalid key of type: ' . $key->type . '.  Expected simple.');
		}

		// Initialise variables.
		$encrypted = '';
		$tmp = $key->private;

		// Split up the input into a character array and get the number of characters.
		$chars = preg_split('//', $data, -1, PREG_SPLIT_NO_EMPTY);
		$charCount = count($chars);

		// Repeat the key as many times as necessary to ensure that the key is at least as long as the input.
		for ($i = 0; $i < $charCount; $i = strlen($tmp))
		{
			$tmp = $tmp . $tmp;
		}

		// Get the XOR values between the ASCII values of the input and key characters for all input offsets.
		for ($i = 0; $i < $charCount; $i++)
		{
			$encrypted .= $this->_intToHex(ord($tmp[$i]) ^ ord($chars[$i]));
		}

		return $encrypted;
	}

	/**
	 * Method to generate a new encryption key[/pair] object.
	 *
	 * @param   array   $options  Key generation options.
	 * @return  object
	 */
	public function generateKey(array $options = array())
	{
		// Create the new encryption key[/pair] object.
		$key = new Key('simple');

		// Just a random key of a given length.
		$key->private = $this->_getRandomKey();
		$key->public  = $key->private;

		return $key;
	}

	/**
	 * Method to generate a random key of a given length.
	 *
	 * @param   integer  $length  The length of the key to generate.
	 * @return  string
	 */
	private function _getRandomKey($length = 256)
	{
		// Initialise variables.
		$key = '';
		$salt = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$saltLength = strlen($salt);

		// Build the random key.
		for ($i = 0; $i < $length; $i++)
		{
			$key .= $salt[mt_rand(0, $saltLength - 1)];
		}

		return $key;
	}

	/**
	 * Convert hex to an integer
	 *
	 * @param   string   $s  The hex string to convert.
	 * @param   integer  $i  The offset?
	 * @return  integer
	 */
	private function _hexToInt($s, $i)
	{
		// Initialise variables.
		$j = (int) $i * 2;
		$k = 0;
		$s1 = (string) $s;

		// Get the character at position $j.
		$c = substr($s1, $j, 1);

		// Get the character at position $j + 1.
		$c1 = substr($s1, $j + 1, 1);

		switch ($c)
		{
			case 'A':
				$k += 160;
				break;
			case 'B':
				$k += 176;
				break;
			case 'C':
				$k += 192;
				break;
			case 'D':
				$k += 208;
				break;
			case 'E':
				$k += 224;
				break;
			case 'F':
				$k += 240;
				break;
			case ' ':
				$k += 0;
				break;
			default:
				(int) $k = $k + (16 * (int) $c);
				break;
		}

		switch ($c1)
		{
			case 'A':
				$k += 10;
				break;
			case 'B':
				$k += 11;
				break;
			case 'C':
				$k += 12;
				break;
			case 'D':
				$k += 13;
				break;
			case 'E':
				$k += 14;
				break;
			case 'F':
				$k += 15;
				break;
			case ' ':
				$k += 0;
				break;
			default:
				$k += (int) $c1;
				break;
		}

		return $k;
	}

	/**
	 * Convert hex to an array of integers
	 *
	 * @param   string  $hex  The hex string to convert to an integer array.
	 * @return  array   An array of integers.
	 */
	private function _hexToIntArray($hex)
	{
		$array = array();

		$j = (int) strlen($hex) / 2;

		for ($i = 0; $i < $j; $i++)
		{
			$array[$i] = (int) $this->_hexToInt($hex, $i);
		}

		return $array;
	}

	/**
	 * Convert an integer to a hexadecimal string.
	 *
	 * @param   integer  $i  An integer value to convert to a hex string.
	 * @return  string
	 */
	private function _intToHex($i)
	{
		// Sanitize the input.
		$i = (int) $i;

		// Get the first character of the hexadecimal string if there is one.
		$j = (int) ($i / 16);
		if ($j === 0)
		{
			$s = ' ';
		}
		else
		{
			$s = strtoupper(dechex($j));
		}

		// Get the second character of the hexadecimal string.
		$k = $i - $j * 16;
		$s = $s . strtoupper(dechex($k));

		return $s;
	}
}
