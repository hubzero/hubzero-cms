<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Mailto\Site\Helpers;

use stdClass;
use App;

/**
 * Mailto helper
 */
abstract class Mailto
{
	/**
	 * Adds a URL to the mailto system and returns the hash
	 *
	 * @param   string  $url
	 * @return  string  URL hash
	 */
	public static function addLink($url)
	{
		$hash = sha1($url);

		self::cleanHashes();

		$session = App::get('session');
		$mailto_links = $session->get('com_mailto.links', array());
		if (!isset($mailto_links[$hash]))
		{
			$mailto_links[$hash] = new stdClass();
		}
		$mailto_links[$hash]->link = $url;
		$mailto_links[$hash]->expiry = time();

		$session->set('com_mailto.links', $mailto_links);

		return $hash;
	}

	/**
	 * Checks if a URL is a Flash file
	 *
	 * @param   string  $hash
	 * @return  string  URL
	 */
	public static function validateHash($hash)
	{
		$retval = false;

		self::cleanHashes();

		$mailto_links = App::get('session')->get('com_mailto.links', array());

		if (isset($mailto_links[$hash]))
		{
			$retval = $mailto_links[$hash]->link;
		}

		return $retval;
	}

	/**
	 * Cleans out old hashes
	 *
	 * @param   integer  $lifetime
	 * @return  void
	 */
	public static function cleanHashes($lifetime = 1440)
	{
		// flag for if we've cleaned on this cycle
		static $cleaned = false;

		if (!$cleaned)
		{
			$past = time() - $lifetime;
			$session = App::get('session');
			$mailto_links = $session->get('com_mailto.links', array());
			foreach ($mailto_links as $index => $link)
			{
				if ($link->expiry < $past)
				{
					unset($mailto_links[$index]);
				}
			}
			$session->set('com_mailto.links', $mailto_links);

			$cleaned = true;
		}
	}

	/**
	 * Cleans any injected headers from the email body.
	 *
	 * @param   string  $body  email body string.
	 * @return  string  Cleaned email body string.
	 */
	public static function cleanBody($body)
	{
		// Strip all email headers from a string
		return preg_replace("/((From:|To:|Cc:|Bcc:|Subject:|Content-type:) ([\S]+))/", '', $body);
	}

	/**
	 * Cleans any injected headers from the subject string.
	 *
	 * @param   string  $subject  email subject string.
	 * @return  string  Cleaned email subject string.
	 */
	public static function cleanSubject($subject)
	{
		return preg_replace("/((From:|To:|Cc:|Bcc:|Content-type:) ([\S]+))/", '', $subject);
	}

	/**
	 * Verifies that an email address does not have any extra headers injected into it.
	 *
	 * @param   string  $address  email address.
	 * @return  mixed   email address string or boolean false if injected headers are present.
	 */
	public static function cleanAddress($address)
	{
		if (preg_match("[\s;,]", $address))
		{
			return false;
		}
		return $address;
	}
}
