<?php

namespace Components\Supportstats\Helpers;

use \Hubzero\Encryption\Encrypter;
use \Hubzero\Encryption\Cipher\Simple;
use \Hubzero\Encryption\Key;

class UrlHelper
{

	public static function appendToUrl($url, $parameters)
	{
		$index = 0;

		foreach ($parameters as $key => $value)
		{
			$separator = $index === 0 ? '?' : '&';
			$url .= "{$separator}{$key}=" . urlencode($value);
			$index++;
		}

		return $url;
	}

	public static function encryptParamValue($value, $key)
	{
		$encrypter = self::_getEncrypter($key);
		$encryptedValue = $encrypter->encrypt($value);
		$encryptedValue = base64_encode($encryptedValue);
		$encryptedValue = urlencode($encryptedValue);

		return $encryptedValue;
	}

	protected static function _getEncrypter($key)
	{
		return new Encrypter(
			new Simple,
			new Key('simple', $key, $key)
		);
	}

}
