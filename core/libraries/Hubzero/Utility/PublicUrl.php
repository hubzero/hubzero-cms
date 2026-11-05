<?php
/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * A URL the server may fetch: one whose host has only public addresses,
 * fetched from those addresses (a guard against server-side request forgery)
 *
 *     if (!($target = PublicUrl::check($url)))  { refuse }
 *     $ch = curl_init();
 *     PublicUrl::apply($ch, $target);
 *     ... other options, CURLOPT_FOLLOWLOCATION off (check each hop) ...
 *     curl_exec($ch);
 *     if (!PublicUrl::fetchedFromChecked($ch, $target))  { discard }
 *
 * check() refuses what PHP's parse_url() and curl could read as different
 * hosts (user info, a backslash), so the host checked is the host fetched;
 * resolves the host, IPv4 and IPv6, and requires every address to be
 * public (Ip::isPublic()); and gives the URL rebuilt from the parts it
 * checked, with a CURLOPT_RESOLVE pin of every address, so curl does not
 * resolve the name again (a name can point at a public address when checked
 * and an internal one when fetched). fetchedFromChecked() then confirms the
 * address curl connected to was one of them.
 */
class PublicUrl
{
	/**
	 * Check a URL, and say how to fetch it
	 *
	 * @param   string  $url
	 * @param   array   $schemes  Schemes allowed
	 * @return  array|false  array(url, host, port, addresses, pins), or false when refused
	 */
	public static function check($url, $schemes = array('http', 'https'))
	{
		$url   = (string) $url;
		$parts = parse_url($url);

		if (!$parts
		 || !isset($parts['scheme'], $parts['host'])
		 || !in_array(strtolower($parts['scheme']), $schemes, true)
		 || isset($parts['user']) || isset($parts['pass'])
		 || strpos($url, '\\') !== false
		 || preg_match('#^[^:]*://[^/?\#]*@#', $url))
		{
			return false;
		}

		$scheme = strtolower($parts['scheme']);

		// curl looks a name up without a trailing dot, so pin it without one
		$host = rtrim(trim($parts['host'], '[]'), '.');
		$port = isset($parts['port']) ? (int) $parts['port'] : ($scheme == 'https' ? 443 : 80);

		if ($host === '')
		{
			return false;
		}

		$addresses = Ip::resolve($host);

		if (!$addresses)
		{
			return false;
		}

		foreach ($addresses as $address)
		{
			if (!Ip::isPublic($address))
			{
				return false;
			}
		}

		$literal   = (bool) filter_var($host, FILTER_VALIDATE_IP);
		$authority = ($literal && strpos($host, ':') !== false) ? '[' . $host . ']' : $host;

		$fetch = $scheme . '://' . $authority . (isset($parts['port']) ? ':' . $port : '')
			. (isset($parts['path']) && $parts['path'] !== '' ? $parts['path'] : '/')
			. (isset($parts['query']) ? '?' . $parts['query'] : '');

		return array(
			'url'       => $fetch,
			'host'      => $host,
			'port'      => $port,
			'addresses' => $addresses,
			// An address literal has no name to re-point, and needs no pin
			'pins'      => $literal ? array() : array(self::pin($host, $port, $addresses))
		);
	}

	/**
	 * Point a curl handle at a checked URL, pinned to its checked addresses
	 *
	 * @param   resource|\CurlHandle  $ch
	 * @param   array                 $target  From check()
	 * @return  void
	 */
	public static function apply($ch, $target)
	{
		curl_setopt($ch, CURLOPT_URL, $target['url']);

		if ($target['pins'])
		{
			curl_setopt($ch, CURLOPT_RESOLVE, $target['pins']);
		}
	}

	/**
	 * Whether curl connected to one of the checked addresses
	 *
	 * @param   resource|\CurlHandle  $ch
	 * @param   array                 $target  From check()
	 * @return  bool
	 */
	public static function fetchedFromChecked($ch, $target)
	{
		return in_array(Ip::normalize(curl_getinfo($ch, CURLINFO_PRIMARY_IP)), $target['addresses'], true);
	}

	/**
	 * A CURLOPT_RESOLVE entry for every address, so curl can use whichever
	 * family the server reaches
	 *
	 * IPv4 first, for a libcurl that takes only one address (before 7.59);
	 * before 7.57 an IPv6 address is not given in brackets.
	 *
	 * @param   string   $host
	 * @param   integer  $port
	 * @param   array    $addresses
	 * @return  string
	 */
	protected static function pin($host, $port, $addresses)
	{
		$curl = curl_version();

		usort($addresses, function ($a, $b)
		{
			return (strpos($a, ':') !== false) - (strpos($b, ':') !== false);
		});

		if (version_compare($curl['version'], '7.59.0', '<'))
		{
			$addresses = array_slice($addresses, 0, 1);
		}

		$brackets = version_compare($curl['version'], '7.57.0', '>=');

		foreach ($addresses as $k => $address)
		{
			$addresses[$k] = ($brackets && strpos($address, ':') !== false) ? '[' . $address . ']' : $address;
		}

		return $host . ':' . $port . ':' . implode(',', $addresses);
	}
}
