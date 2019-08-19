<?php
/*------------------------------------------------------------------------------
** File:        IPv6Net.php
** Description: PHP class for an IPv6 subnet.
** Version:     0.1
** Author:      Juergen Enge
** Email:       juergen dot enge at hfg dot edu
** Homepage:    http://www.hfg.edu
**------------------------------------------------------------------------------
** COPYRIGHT (c) 2011 Juergen Enge
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; version 2 of the License.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
**------------------------------------------------------------------------------
* Demo Code
*
* echo "<pre>\n";
*
* $net = '2001:7C0:403:C000::1/64';  // works also: 192.168.1.1/24 or ::192.168.1.1/120
* $test = '2001:7C0:403:C000:12::2';
*
* $net = new IPV6Net($net);
*
* echo "Subnet: {$net}<br />\n";
*
* echo 'Network: '.$net->getNetwork(true)."<br />\n";
* echo 'Broadcast: '.$net->getBroadcast(true)."<br />\n";
* echo "contains($test) = ".($net->contains($test))."<br />\n";;
* echo "</pre>\n";
*/

/**
 * Represents an IPv6 subnet
 *
 * the IPv6Net class is used to describe a subnet based on an ip-address and a netmask.
 */
class IPv6Net
{
	private $net_addr; //!< a binary representation of the network
	private $net_addr_long; //!< a 128bit integer of ipv6 addr
	private $net_mask; //!< a binary representation of the network mask
	private $net_mask_long; //!< a 128bit integer of ipv6 network mask
	private $net_mask_bits; //!< an integer with the number of bits set in the network mask
	private $net_broadcast; //!< a binary representation of the network broadcast
	private $net_broadcast_long; //!< a 128bit integer of ipv6 network broadcast
	private $ipv4; //!< a boolean indicating whether it is an IPv4 address
	private $valid; //!< a boolean indicating whether the ip address is valid

	/**
	 * checks for ipv4 address
	 *
	 * @param   int $addr a string with an ip address
	 * @return true, if $addr is an ipv4 address
	 */
	public static function isIPv4($addr)
	{
		return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $addr);
	}

	/**
	 * Converts a packed internet address to a gmp resource
	 *
	 * @param   int $in_addr a packed internet addr
	 * @return  a gmp resource
	 */
	private static function inet_ntogmp($addr)
	{
		// 16*8 == 128
		$gmp = gmp_init(0);
		for ($bits = 0; $bits < 16; $bits++)
		{
			$byte = ord($addr[15-$bits]);
			for ($b = 0; $b < 8; $b++)
			{
				gmp_setbit($gmp, $bits*8+$b, $byte & (1<<$b));
			}
		}

		return $gmp;
	}

	/**
	 * Converts a gmp resource to an expanded internet address
	 *
	 * @param   int $gmp a gmp resource
	 * @return  an expanded internet address
	 */
	private static function inet_gmptofull($gmp)
	{
		$str = gmp_strval($gmp, 16);
		for ($i = strlen($str); $i < 32; $i++)
		{
			$str = '0'.$str;
		}
		$ret = '';
		for ($i = 0; $i < 8; $i++)
		{
			$ret .= substr($str, $i*4, 4).':';
		}
		return substr($ret, 0, -1);
	}

	/**
	 * Converts a gmp resource to a packed internet address
	 *
	 * @param   int $gmp a gmp resource
	 * @return a packed internet address
	 */
	private static function inet_gmpton($gmp)
	{
		// 16*8 == 128
		$addr = '';
		for ($bits = 0; $bits < 16; $bits++)
		{
			$byte = 0;
			for ($b = 0; $b < 8; $b++)
			{
				if (gmp_testbit($gmp, (15-$bits)*8+$b))
				{
					$byte |= 1<<$b;
				}
			}
			$addr .= chr($byte);
		}
//		echo gmp_strval($gmp, 16).' -> '.inet_ntop($addr)."<br />\n";
		return $addr;
	}

	/**
	 * Converts a  internet address to an expanded IPv6 address
	 *
	 * @param   int $addr a string with an internet address
	 * @return an expanded internet address
	 */
	public static function inet_ptofull($addr)
	{
		if (self::isIPv4($addr))
		{
			$addr = '::'.$addr;
		}
		$net_addr = @inet_pton($addr);
		if ($net_addr == false)
		{
			throw new Exception("invalid ip address {$addr}");
		}
		$net_addr_long = self::inet_ntogmp($net_addr);
		return self::inet_gmptofull($net_addr_long);
	}

	/**
	 * constructor
	 *
	 * @param   int $addr a string with an ip address
	 * @throws Exception if address is invalid
	 */
	public function __construct($addr)
	{
		$this->valid = false;
		// check for netmask
		$cx = strpos($addr, '/');
		if ($cx)
		{
			$mask = trim(substr($addr, $cx+1));
			$addr = trim(substr($addr, 0, $cx));
		}
		else
		{
			$mask = null;
		}
		$this->ipv4 = $this->isIPv4($addr);
		if ($this->ipv4)
		{
			$addr = '::'.$addr;
			if ($mask != null)
			{
				if ($this->isIPv4($mask))
				{
					$mask = '::'.$mask;
				}
			}
		}

		if ($mask == null)
		{
			$mask = 128;
		}
		$this->setIPv6($addr, $mask);
	}

	/**
	 * To string
	 *
	 * @return  string
	 */
	public function __toString()
	{
		if (!$this->net_addr)
		{
			return '::0/128';
		}
		return inet_ntop($this->net_addr).'/'.$this->net_mask_bits;
	}

	/**
	 * get the network as ipv6
	 *
	 * @param   int $full a boolean indicating whether the resultung ipv6 address is expanded
	 * @return a string with an ipv6 address
	 */
	public function getNetwork($full = false)
	{
		if (!$this->valid)
		{
			return null;
		}
		if (!$full)
		{
			return inet_ntop($this->net_addr);
		}
		else
		{
			return $this->inet_gmptofull($this->net_addr_long);
		}
	}

	/**
	 * get the network as ipv4 integer
	 *
	 * @return an integer with the ipv4 address
	 */
	public function getNetworkIPv4()
	{
		if (!$this->valid)
		{
			return null;
		}
		if (gmp_cmp('4294967295', $this->net_addr_long) > 0)
		{
			return gmp_strval($this->net_addr_long);
		}
		else
		{
			return '4294967295';
		}
	}

	/**
	 * get the broadcast as ipv4 integer
	 *
	 * @return an integer with the ipv4 address
	 */
	public function getBroadcastIPv4()
	{
		if (!$this->valid)
		{
			return null;
		}
		if (gmp_cmp('4294967295', $this->net_broadcast_long) > 0)
		{
			return gmp_strval($this->net_broadcast_long);
		}
		else
		{
			return '4294967295';
		}
	}

	/**
	 * get the broadcast as ipv6
	 *
	 * @param   int $full a boolean indicating whether the resultung ipv6 address is expanded
	 * @return a string with an ipv6 address
	 */
	public function getBroadcast($full = false)
	{
		if (!$this->valid)
		{
			return null;
		}
		if (!$full)
		{
			return inet_ntop($this->net_broadcast);
		}
		else
		{
			return $this->inet_gmptofull($this->net_broadcast_long);
		}
	}

	/**
	 * checks whether address is ipv4
	 *
	 * @return a boolean value indicating whether the subnet is ipv4
	 */
	public function ipv4()
	{
		return $this->ipv4;
	}

	/**
	 * checks whether address is valid
	 *
	 * @return a boolean value indicating whether address is valid
	 */
	public function valid()
	{
		return $this->valid;
	}

	/**
	 * calculates size of the subnet
	 *
	 * @return an integer with the number of adresses within the subnetz
	 */
	public function size()
	{
		if (!$this->valid)
		{
			return 0;
		}
		return gmp_intval(gmp_add($this->net_broadcast_long, gmp_neg($this->net_addr_long)));
	}

	/**
	 * Tests whether the ipv6 address is part of the network
	 *
	 * @param   int  $ip a string with the ipv4/v6 address to be checked
	 * @return  boolean indicating whether the address is part of the network
	 */
	public function contains($ip)
	{
		if (!$this->valid)
		{
			return false;
		}
		if ($this->isIPv4($ip))
		{
			$ip = '::'.$ip;
		}
		$addr = @inet_pton($ip);
		if ($addr === false)
		{
			return false;
		}
		$gmp = $this->inet_ntogmp($addr);
		return (gmp_cmp($this->net_addr_long, $gmp) <= 0 && gmp_cmp($gmp, $this->net_broadcast_long) <= 0);
	}

	/**
	 * Set internal subnet data (ipv6 only)
	 *
	 * @param   int $addr a string with the ip address
	 * @param   int $mask a string with the network mask (bits or complete mask)
	 * @return  void
	 */
	private function setIPv6($addr, $mask)
	{
		$this->net_addr = @inet_pton($addr);
		if ($this->net_addr == false)
		{
			throw new Exception("invalid ip address {$addr}");
		}
		$this->valid = true;
		$this->net_addr_long = $this->inet_ntogmp($this->net_addr);
		//$this->inet_gmpton($this->net_addr_long);

		// set the netmask
		if (preg_match('/^[0-9]+$/', $mask))
		{
			$this->net_mask_bits = intval($mask);
			if ($this->ipv4 && $this->net_mask_bits != 0)
			{
				$this->net_mask_bits += 96;
			}
			$this->net_mask_long = gmp_mul(gmp_sub(gmp_pow(2, $this->net_mask_bits), 1), gmp_pow(2, 128-$this->net_mask_bits));
			//			echo gmp_strval($this->net_mask_long, 2)."<br />\n";
			$this->net_mask = $this->inet_gmpton($this->net_mask_long);
		}
		else
		{
			$this->net_mask = inet_pton($mask);
			$this->net_mask_long = $this->inet_ntogmp($this->netmask);
			$this->net_mask_bits = gmp_scan0($this->net_mask_long, 0);
		}

		// normalize it...
		$this->net_addr_long = gmp_and($this->net_addr_long, $this->net_mask_long);
		$this->net_addr = $this->inet_gmpton($this->net_addr_long);
		$this->net_broadcast_long = gmp_or($this->net_addr_long, gmp_sub(gmp_pow(2, 128-$this->net_mask_bits), 1));
		$this->net_broadcast = $this->inet_gmpton($this->net_broadcast_long);
	}

	/**
	 * Subnet this network into smaller networks (thanks to Martin Baum)
	 *
	 * example $net = new IPv6Net("2a00:2000:1::/48"); var_dump($net->subNetTo(56));
	 *                 will subnet a /48 into 256 /56 networks
	 *
	 * @param   int $iNetMaskBits an integer with the number of bits to
	 * @param   int $iMaxValues an integer with the max number of subnets to return. Default: 1024
	 * @param   int $iStartAt an integer with the offset to start from (0=first subnet, 1=second subnet, ...)
	 * @return  Array containing human redable representation of subnets
	 */
	public function subNetTo($iNetMaskBits, $iMaxValues=1024, $iStartAt=0)
	{
		$aRet = array();

		$iNetmaskDiff = $iNetMaskBits - $this->net_mask_bits;

		$rStep = gmp_pow(2, 128-$iNetMaskBits);
		$rCurr = gmp_add($this->net_addr_long, gmp_mul($iStartAt, $rStep));

		for ($i=0; $i<$iMaxValues; $i++)
		{
			if (gmp_cmp($rCurr, $this->net_broadcast_long) > 0)
			{
				break;
			}
			$aRet[] = inet_ntop($this->inet_gmpton($rCurr))."/$iNetMaskBits";
			$rCurr = gmp_add($rCurr, $rStep);
		}
		return $aRet;
	}
}
