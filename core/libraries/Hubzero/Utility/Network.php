<?php
/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * An IPv4 or IPv6 network, written as an address and a prefix length
 *
 * Addresses are compared as the bytes inet_pton() gives, so both families
 * are handled alike and no big-number library is needed. An IPv6 address
 * that carries an IPv4 one, ::ffff:192.0.2.1, is compared as that IPv4
 * address, which is how a dual-stack server reports an IPv4 visitor.
 */
class Network
{
	/**
	 * The network's address, packed and masked
	 *
	 * @var  string
	 */
	private $address;

	/**
	 * The mask, packed, the same length as the address
	 *
	 * @var  string
	 */
	private $mask;

	/**
	 * Whether the network was written with a prefix of 0, which is every
	 * address of either family
	 *
	 * @var  bool
	 */
	private $everyone = false;

	/**
	 * Read a network: 192.0.2.0/24, 192.0.2.0/255.255.255.0, 2001:db8::/32,
	 * or a single address
	 *
	 * A prefix of 0 is every address, IPv4 and IPv6 alike; 0.0.0.0/0 in a
	 * list means everyone.
	 *
	 * @param   string  $cidr
	 * @return  static|null  Null for anything that is not a network
	 */
	public static function parse($cidr)
	{
		$parts    = explode('/', trim((string) $cidr), 2);
		$offset   = 0;
		$everyone = false;
		$address  = self::pack($parts[0], $offset);

		if ($address === null)
		{
			return null;
		}

		$bits = strlen($address) * 8;

		if (!isset($parts[1]))
		{
			$prefix = $bits;
		}
		elseif (preg_match('/^[0-9]{1,3}$/', $parts[1]))
		{
			// An IPv4 network written in IPv6 counts its prefix from 128, so
			// ::ffff:0:0/96 is all of IPv4 and nothing else. Only a prefix
			// written as 0 is everyone.
			$prefix = (int) $parts[1] - $offset;

			if ((int) $parts[1] == 0)
			{
				$prefix   = 0;
				$everyone = true;
			}
		}
		elseif ($bits == 32 && ($mask = @inet_pton($parts[1])) !== false && strlen($mask) == 4)
		{
			$prefix   = self::prefixOf($mask);
			$everyone = ($prefix === 0);
		}
		else
		{
			return null;
		}

		if ($prefix === null || $prefix < 0 || $prefix > $bits)
		{
			return null;
		}

		$network = new static;
		$network->everyone = $everyone;
		$network->mask     = self::maskOf($prefix, strlen($address));
		$network->address  = $address & $network->mask;

		return $network;
	}

	/**
	 * Whether an address is in the network
	 *
	 * @param   string  $ip
	 * @return  bool
	 */
	public function contains($ip)
	{
		$offset = 0;
		$ip = self::pack($ip, $offset);

		if ($ip === null)
		{
			return false;
		}

		// A prefix written as 0 is everyone, of either family
		if ($this->everyone)
		{
			return true;
		}

		return strlen($ip) == strlen($this->address) && ($ip & $this->mask) === $this->address;
	}

	/**
	 * Whether the network is every address of either family, written with a
	 * prefix of 0
	 *
	 * @return  bool
	 */
	public function isEveryone()
	{
		return $this->everyone;
	}

	/**
	 * The lowest address in the network
	 *
	 * @return  string
	 */
	public function first()
	{
		return inet_ntop($this->address);
	}

	/**
	 * The highest address in the network
	 *
	 * @return  string
	 */
	public function last()
	{
		return inet_ntop($this->address | ~$this->mask);
	}

	/**
	 * An address as inet_pton() packs it, with an IPv4-mapped IPv6
	 * address unpacked to its IPv4 one
	 *
	 * @param   string   $ip
	 * @param   integer  $offset  Set to 96 when the address was IPv4-mapped
	 * @return  string|null
	 */
	private static function pack($ip, &$offset)
	{
		$packed = @inet_pton(trim((string) $ip));

		if ($packed === false)
		{
			return null;
		}

		if (strlen($packed) == 16 && substr($packed, 0, 12) === str_repeat("\0", 10) . "\xff\xff")
		{
			$offset = 96;
			return substr($packed, 12);
		}

		return $packed;
	}

	/**
	 * A packed mask of the given prefix length
	 *
	 * @param   integer  $prefix
	 * @param   integer  $bytes
	 * @return  string
	 */
	private static function maskOf($prefix, $bytes)
	{
		$mask = str_repeat("\xff", intdiv($prefix, 8));

		if ($prefix % 8)
		{
			$mask .= chr((0xff << (8 - $prefix % 8)) & 0xff);
		}

		return str_pad($mask, $bytes, "\0");
	}

	/**
	 * The prefix length of a packed mask, or null if its ones are not all
	 * at the front
	 *
	 * @param   string  $mask
	 * @return  integer|null
	 */
	private static function prefixOf($mask)
	{
		$bits = '';

		foreach (str_split($mask) as $byte)
		{
			$bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
		}

		return preg_match('/^1*0*$/', $bits) ? strlen(rtrim($bits, '0')) : null;
	}
}
