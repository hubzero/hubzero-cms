<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Encryption;

/**
 * Class for handling basic encryption/decryption of data.
 *
 * Inspired by Joomla's JCrypt class
 */
class Encrypter
{
	/**
	 * The encryption cipher object.
	 *
	 * @var  object
	 */
	private $cipher;

	/**
	 * The encryption key[/pair)].
	 *
	 * @var  object
	 */
	private $key;

	/**
	 * Object Constructor takes an optional key to be used for encryption/decryption. If no key is given then the
	 * secret word from the configuration object is used.
	 *
	 * @param   object  $cipher  The encryption cipher object.
	 * @param   object  $key     The encryption key[/pair)].
	 * @return  void
	 */
	public function __construct(Cipher $cipher = null, Key $key = null)
	{
		// Set the encryption key[/pair)].
		$this->key = $key;

		// Set the encryption cipher.
		$this->cipher = isset($cipher) ? $cipher : new Simple;
	}

	/**
	 * Method to decrypt a data string.
	 *
	 * @param   string  $data  The encrypted string to decrypt.
	 * @return  string  The decrypted data string.
	 */
	public function decrypt($data)
	{
		return $this->cipher->decrypt($data, $this->key);
	}

	/**
	 * Method to encrypt a data string.
	 *
	 * @param   string  $data  The data string to encrypt.
	 * @return  string  The encrypted data string.
	 */
	public function encrypt($data)
	{
		return $this->cipher->encrypt($data, $this->key);
	}

	/**
	 * Method to generate a new encryption key[/pair] object.
	 *
	 * @param   array  $options  Key generation options.
	 * @return  object
	 */
	public function generateKey(array $options = array())
	{
		return $this->cipher->generateKey($options);
	}

	/**
	 * Method to set the encryption key[/pair] object.
	 *
	 * @param   object  $key  The key object to set.
	 * @return  object
	 */
	public function setKey(Key $key)
	{
		$this->key = $key;

		return $this;
	}

	/**
	 * Generate random bytes.
	 *
	 * @param   integer  $length  Length of the random data to generate
	 * @return  string   Random binary data
	 */
	public static function genRandomBytes($length = 16)
	{
		$sslStr = '';

		// if a secure randomness generator exists and we don't
		// have a buggy PHP version use it.
		if (function_exists('openssl_random_pseudo_bytes')
			&& (version_compare(PHP_VERSION, '5.3.4') >= 0 || IS_WIN))
		{
			$sslStr = openssl_random_pseudo_bytes($length, $strong);
			if ($strong)
			{
				return $sslStr;
			}
		}

		// Collect any entropy available in the system along with a number
		// of time measurements of operating system randomness.
		$bitsPerRound  = 2;
		$maxTimeMicro  = 400;
		$shaHashLength = 20;
		$randomStr     = '';
		$total         = $length;

		// Check if we can use /dev/urandom.
		$urandom = false;
		$handle  = null;

		// This is PHP 5.3.3 and up
		if (function_exists('stream_set_read_buffer') && @is_readable('/dev/urandom'))
		{
			$handle = @fopen('/dev/urandom', 'rb');
			if ($handle)
			{
				$urandom = true;
			}
		}

		while ($length > strlen($randomStr))
		{
			$bytes = ($total > $shaHashLength)? $shaHashLength : $total;
			$total -= $bytes;

			// Collect any entropy available from the PHP system and filesystem.
			// If we have ssl data that isn't strong, we use it once.
			$entropy  = rand() . uniqid(mt_rand(), true) . $sslStr;
			$entropy .= implode('', @fstat(fopen(__FILE__, 'r')));
			$entropy .= memory_get_usage();
			$sslStr = '';

			if ($urandom)
			{
				stream_set_read_buffer($handle, 0);
				$entropy .= @fread($handle, $bytes);
			}
			else
			{
				// There is no external source of entropy so we repeat calls
				// to mt_rand until we are assured there's real randomness in
				// the result.
				//
				// Measure the time that the operations will take on average.
				$samples = 3;
				$duration = 0;
				for ($pass = 0; $pass < $samples; ++$pass)
				{
					$microStart = microtime(true) * 1000000;
					$hash = sha1(mt_rand(), true);
					for ($count = 0; $count < 50; ++$count)
					{
						$hash = sha1($hash, true);
					}
					$microEnd = microtime(true) * 1000000;
					$entropy .= $microStart . $microEnd;
					if ($microStart > $microEnd)
					{
						$microEnd += 1000000;
					}
					$duration += $microEnd - $microStart;
				}
				$duration = $duration / $samples;

				// Based on the average time, determine the total rounds so that
				// the total running time is bounded to a reasonable number.
				$rounds = (int) (($maxTimeMicro / $duration) * 50);

				// Take additional measurements. On average we can expect
				// at least $bitsPerRound bits of entropy from each measurement.
				$iter = $bytes * (int) ceil(8 / $bitsPerRound);
				for ($pass = 0; $pass < $iter; ++$pass)
				{
					$microStart = microtime(true);
					$hash = sha1(mt_rand(), true);
					for ($count = 0; $count < $rounds; ++$count)
					{
						$hash = sha1($hash, true);
					}
					$entropy .= $microStart . microtime(true);
				}
			}

			$randomStr .= sha1($entropy, true);
		}

		if ($urandom)
		{
			@fclose($handle);
		}

		return substr($randomStr, 0, $length);
	}

	/**
	 * A timing safe comparison method. This defeats hacking
	 * attempts that use timing based attack vectors.
	 *
	 * @param   string   $known    A known string to check against.
	 * @param   string   $unknown  An unknown string to check.
	 * @return  boolean  True if the two strings are exactly the same.
	 */
	public static function timingSafeCompare($known, $unknown)
	{
		// Prevent issues if string length is 0
		$known   .= chr(0);
		$unknown .= chr(0);

		$knownLength   = strlen($known);
		$unknownLength = strlen($unknown);

		// Set the result to the difference between the lengths
		$result = $knownLength - $unknownLength;

		// Note that we ALWAYS iterate over the user-supplied length to prevent leaking length info.
		for ($i = 0; $i < $unknownLength; $i++)
		{
			// Using % here is a trick to prevent notices. It's safe, since if the lengths are different, $result is already non-0
			$result |= (ord($known[$i % $knownLength]) ^ ord($unknown[$i]));
		}

		// They are only identical strings if $result is exactly 0...
		return $result === 0;
	}
}
