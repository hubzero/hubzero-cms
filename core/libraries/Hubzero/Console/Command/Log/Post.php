<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Log;

/**
 * Post log class
 **/
class Post extends Base
{
	/**
	 * Fields available in this log and their default visibility
	 *
	 * @var  array
	 **/
	protected static $fields = array(
		'timestamp' => true,
		'uri'       => true,
		'referrer'  => true,
		'data'      => true
	);

	/**
	 * If dates/times are present, how are they formatted
	 *
	 * @var  string
	 **/
	protected static $dateFormat = "Y-m-d\TH:i:s.uP";

	/**
	 * Parses
	 *
	 * @return  void
	 **/
	public static function parseData($value)
	{
		$ciphertext = base64_decode($value);

		// Get the IV
		$ivSize = openssl_cipher_iv_length('AES-256-CBC');
		$iv = substr($ciphertext, 0, $ivSize);

		// Get just the cipher without the IV
		$ciphertext = substr($ciphertext, $ivSize);

		// Generate key and decrypt
		$key       = md5(\App::get('config')->get('secret'));
		$plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);

		return $plaintext;
	}

	/**
	 * Get log path
	 *
	 * @return  string
	 **/
	public static function path()
	{
		$dir = \Config::get('log_path');

		if (is_dir('/var/log/hubzero-cms'))
		{
			$dir = '/var/log/hubzero-cms';
		}

		$path = $dir . '/cmspost.log';

		return $path;
	}
}
