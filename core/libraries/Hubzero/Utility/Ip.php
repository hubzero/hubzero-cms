<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * IP address class
 *
 * Handles IPv4 and IPv6 alike, as the bytes inet_pton() gives. An
 * IPv4-mapped IPv6 address, ::ffff:192.0.2.1, is taken as the IPv4 address
 * it carries, which is how a dual-stack server reports an IPv4 visitor.
 */
class Ip
{
	/**
	 * The prefix of an IPv4-mapped IPv6 address, ::ffff:0:0/96
	 *
	 * @var  string
	 */
	const MAPPED_PREFIX = "\0\0\0\0\0\0\0\0\0\0\xff\xff";

	/**
	 * The instance ip address, as given
	 *
	 * @var  string
	 **/
	private $ip;

	/**
	 * The instance ip address, packed (4 bytes for IPv4, 16 for IPv6), or
	 * false when it is not an address
	 *
	 * @var  string|false
	 **/
	private $packed;

	/**
	 * Constructs a new instance
	 *
	 * @param   string  $ip  The ip address
	 * @return  void
	 **/
	public function __construct($ip)
	{
		$this->ip     = $ip;
		$this->packed = self::pack($ip);
	}

	/**
	 * Checks to see if the ip address is of valid form
	 *
	 * @param   string  $type  The IP Protocol version to validate against
	 * @return  bool
	 **/
	public function isValid($type = 'both')
	{
		$type  = strtolower($type);
		$flags = 0;

		if ($type === 'ipv4')
		{
			$flags = FILTER_FLAG_IPV4;
		}
		if ($type === 'ipv6')
		{
			$flags = FILTER_FLAG_IPV6;
		}

		return (bool) filter_var($this->ip, FILTER_VALIDATE_IP, ['flags' => $flags]);
	}

	/**
	 * Checks to see if the ip address is within a private range
	 *
	 * The RFC 1918 IPv4 ranges, and their IPv6 counterpart, the unique local
	 * addresses fc00::/7 (RFC 4193). Loopback and link-local addresses are
	 * not private here, in either family.
	 *
	 * @return  bool
	 **/
	public function isPrivate()
	{
		foreach (array('10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fc00::/7') as $net)
		{
			if (Network::parse($net)->contains($this->ip))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks to see if the ip address is between the given range (inclusive)
	 *
	 * @param   string  $low   The low end check
	 * @param   string  $high  The high end check
	 * @return  bool
	 **/
	public function isBetween($low, $high)
	{
		return ($this->isAbove($low) && $this->isBelow($high));
	}

	/**
	 * Checks to see if the ip address is greater than or equal to the given
	 *
	 * @param   string  $threshold  The comparison threshold
	 * @return  bool
	 **/
	public function isAbove($threshold)
	{
		return $this->isRelativeTo($threshold);
	}

	/**
	 * Checks to see if the ip address is less than or equal to the given
	 *
	 * @param   string  $threshold  The comparison threshold
	 * @return  bool
	 **/
	public function isBelow($threshold)
	{
		return $this->isRelativeTo($threshold, false);
	}

	/**
	 * Checks to see if the ip address is less than or greater than the given
	 *
	 * An address is never above or below one of the other family.
	 *
	 * @param   string  $threshold  The comparison threshold
	 * @param   bool    $above      Whether to check above or below
	 * @return  bool
	 * @throws  \RuntimeException  When the threshold is not an address
	 **/
	private function isRelativeTo($threshold, $above = true)
	{
		$threshold = self::pack($threshold);

		if ($threshold === false)
		{
			throw new \RuntimeException('Invalid input, not an IP address');
		}

		if ($this->packed === false || strlen($this->packed) != strlen($threshold))
		{
			return false;
		}

		// Packed addresses of one length sort as the numbers they are
		$cmp = strcmp($this->packed, $threshold);

		return $above ? ($cmp >= 0) : ($cmp <= 0);
	}

	/**
	 * An address in one canonical text form
	 *
	 * An IPv4-mapped address becomes the IPv4 one it carries; an IPv6
	 * address is lower case and compressed (RFC 5952), at most 39
	 * characters. Two spellings of one address normalize alike, so the
	 * result can be stored and compared as a string.
	 *
	 * @param   string  $ip
	 * @return  string  Empty when it is not an address
	 */
	public static function normalize($ip)
	{
		$packed = self::pack($ip);

		return $packed === false ? '' : inet_ntop($packed);
	}

	/**
	 * An address as 16 bytes, an IPv4 one as IPv4-mapped IPv6
	 *
	 * All addresses then have one length and sort as the numbers they are,
	 * so ranges can be compared with <= and >= in either family. com_tools
	 * zone locations store them as hex (see its helpers/iprange.php). The
	 * geo database's IPv6 tables (ipcountry6, iplocation6) are keyed by
	 * DECIMAL(39,0) instead, as ip2location ships them: see toDecimal() and
	 * \Hubzero\Geocode\Geocode.
	 *
	 * @param   string  $ip
	 * @return  string|false  False when it is not an address
	 */
	public static function toPacked16($ip)
	{
		$packed = self::pack($ip);

		if ($packed === false)
		{
			return false;
		}

		return strlen($packed) == 4 ? self::MAPPED_PREFIX . $packed : $packed;
	}

	/**
	 * The text form of an address packed in 4 or 16 bytes
	 *
	 * The reverse of toPacked16(): an IPv4-mapped address comes back as IPv4.
	 *
	 * @param   string  $packed
	 * @return  string  Empty when it is not a packed address
	 */
	public static function fromPacked16($packed)
	{
		$packed = (string) $packed;

		if (strlen($packed) != 4 && strlen($packed) != 16)
		{
			return '';
		}

		return self::normalize(inet_ntop($packed));
	}

	/**
	 * An address as the unsigned decimal number it is
	 *
	 * 32 bits for an IPv4 address (an IPv4-mapped one included), 128 for
	 * IPv6, as the DECIMAL(39,0) columns of the ip2location tables hold
	 * them. Computed without GMP or bcmath.
	 *
	 * @param   string  $ip
	 * @return  string|false  Digits only, or false when it is not an address
	 */
	public static function toDecimal($ip)
	{
		$packed = self::pack($ip);

		if ($packed === false)
		{
			return false;
		}

		// 16-bit limbs, most significant first; divide by 10^9 until nothing
		// is left. A remainder times 65536 plus a limb stays under 2^53.
		$limbs  = array_values(unpack('n*', $packed));
		$digits = '';

		while ($limbs)
		{
			$rem      = 0;
			$quotient = array();

			foreach ($limbs as $limb)
			{
				$cur = $rem * 65536 + $limb;
				$q   = intdiv($cur, 1000000000);
				$rem = $cur % 1000000000;

				if ($q || $quotient)
				{
					$quotient[] = $q;
				}
			}

			$limbs  = $quotient;
			$digits = ($limbs ? str_pad((string) $rem, 9, '0', STR_PAD_LEFT) : (string) $rem) . $digits;
		}

		return $digits;
	}

	/**
	 * The addresses a host name resolves to, IPv4 and IPv6
	 *
	 * gethostbynamel() knows only IPv4, so a check made with it alone misses
	 * the AAAA records a client may connect to. An address literal, with or
	 * without the brackets of a URL, resolves to itself.
	 *
	 * @param   string  $host
	 * @return  array   Normalized addresses; empty when it does not resolve
	 */
	public static function resolve($host)
	{
		$host = trim(trim((string) $host), '[]');

		if ($host === '')
		{
			return array();
		}

		if (filter_var($host, FILTER_VALIDATE_IP))
		{
			return array(self::normalize($host));
		}

		$addresses = array();

		if (function_exists('dns_get_record'))
		{
			foreach ((array) @dns_get_record($host, DNS_A | DNS_AAAA) as $record)
			{
				if (!empty($record['ip']))
				{
					$addresses[] = $record['ip'];
				}
				if (!empty($record['ipv6']))
				{
					$addresses[] = $record['ipv6'];
				}
			}
		}

		if (!$addresses)
		{
			$addresses = (array) @gethostbynamel($host);
		}

		return array_values(array_unique(array_filter(array_map(array(__CLASS__, 'normalize'), $addresses))));
	}

	/**
	 * Whether an address is one an outbound request may be sent to
	 *
	 * Refuses private and reserved addresses of both families. An
	 * IPv4-mapped address is judged as the IPv4 one it carries, since
	 * FILTER_FLAG_NO_PRIV_RANGE does not look inside ::ffff:0:0/96 and curl
	 * connects to the IPv4 address. Prefixes that carry another address
	 * inside them are refused too: IPv4-compatible ::/96, NAT64
	 * 64:ff9b::/96, discard-only 100::/64, Teredo 2001::/32 and 6to4
	 * 2002::/16.
	 *
	 * @param   string  $ip
	 * @return  bool
	 */
	public static function isPublic($ip)
	{
		$ip = self::normalize($ip);

		if ($ip === '')
		{
			return false;
		}

		foreach (array('::/96', '64:ff9b::/96', '100::/64', '2001::/32', '2002::/16') as $net)
		{
			if (Network::parse($net)->contains($ip))
			{
				return false;
			}
		}

		return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	/**
	 * An address as inet_pton() packs it, an IPv4-mapped one as IPv4
	 *
	 * @param   string  $ip
	 * @return  string|false
	 */
	private static function pack($ip)
	{
		$packed = @inet_pton(trim((string) $ip));

		if ($packed === false)
		{
			return false;
		}

		if (strlen($packed) == 16 && substr($packed, 0, 12) === self::MAPPED_PREFIX)
		{
			return substr($packed, 12);
		}

		return $packed;
	}
}
