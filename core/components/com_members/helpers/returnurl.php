<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Helpers;

use Hubzero\Utility\Uri;

/**
 * Resolve and sanitize post-action "return" URLs for the account flow.
 *
 * A return URL may be supplied on the request, stored on the member profile so
 * it survives the email-confirmation round-trip, or fall back to a configured
 * site default. Every candidate is validated as internal to the site, so a
 * "return" value can never be used as an open redirect off-site.
 */
class ReturnUrl
{
	/**
	 * Decode (base64 or plain) and validate a return value as an internal URL.
	 *
	 * @param   string  $value  Raw return value (possibly base64-encoded)
	 * @return  string  A safe, internal URL, or '' if empty/off-site/invalid
	 */
	public static function sanitize($value)
	{
		if (!$value)
		{
			return '';
		}

		$url = self::isBase64($value) ? base64_decode($value) : $value;
		$url = urldecode($url);

		if (!$url || !Uri::isInternal($url))
		{
			return '';
		}

		return $url;
	}

	/**
	 * Determine whether a string is genuinely base64-encoded (round-trips),
	 * as opposed to plain text that merely decodes without error.
	 *
	 * @param   string   $value
	 * @return  boolean
	 */
	public static function isBase64($value)
	{
		if (!is_string($value) || $value === '')
		{
			return false;
		}

		$decoded = base64_decode($value, true);

		if ($decoded === false)
		{
			return false;
		}

		return base64_encode($decoded) === $value;
	}

	/**
	 * Resolve a return URL by priority: an explicit request value, then a value
	 * stored on the profile, then a configured default. Each candidate is
	 * sanitized; the first safe internal URL wins.
	 *
	 * @param   string  $param          Raw return value from the request
	 * @param   object  $profile        Member profile (checked for a stored 'return' param)
	 * @param   string  $configDefault  Site default (e.g. the ConfirmationReturn setting)
	 * @return  string  A safe internal URL, or ''
	 */
	public static function resolve($param = '', $profile = null, $configDefault = '')
	{
		$url = self::sanitize($param);

		if (!$url && is_object($profile) && method_exists($profile, 'getParam'))
		{
			$url = self::sanitize($profile->getParam('return', ''));
		}

		if (!$url && $configDefault)
		{
			$url = self::sanitize($configDefault);
		}

		return $url;
	}
}
