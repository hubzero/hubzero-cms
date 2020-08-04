<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;

/**
 * Ip utility test
 */
class IpTest extends Basic
{
	/**
	 * Tests is valid ip check
	 *
	 * @return  void
	 **/
	public function testIsValid()
	{
		$ip = new \Hubzero\Utility\Ip('192.168.0.1');

		$this->assertTrue($ip->isValid(), 'Basic IPv4 address did not validate');

		$ip = new \Hubzero\Utility\Ip('256.256.256.256');

		$this->assertFalse($ip->isValid(), 'Invalid IPv4 validated as true');

		$ip = new \Hubzero\Utility\Ip('684D:1111:222:3333:4444:5555:6:77');

		$this->assertTrue($ip->isValid('ipv6'), 'Basic IPv6 address did not validate');

		$ip = new \Hubzero\Utility\Ip('192.168.0.1');

		$this->assertFalse($ip->isValid('ipv6'), 'Basic IPv4 address validated as IPv6');

		$ip = new \Hubzero\Utility\Ip('684D:1111:222:3333:4444:5555:6:77');

		$this->assertFalse($ip->isValid('ipv4'), 'Basic IPv6 address validated as IPv4');
	}

	/**
	 * Tests is private ip check
	 *
	 * @return  void
	 **/
	public function testIsPrivate()
	{
		$ip = new \Hubzero\Utility\Ip('192.168.255.255');
		$this->assertTrue($ip->isPrivate(), 'Basic IPv4 address did not identify as private');

		$ip = new \Hubzero\Utility\Ip('172.16.0.1');
		$this->assertTrue($ip->isPrivate(), 'Basic IPv4 address did not identify as private');

		$ip = new \Hubzero\Utility\Ip('10.5.20.135');
		$this->assertTrue($ip->isPrivate(), 'Basic IPv4 address did not identify as private');

		$ip = new \Hubzero\Utility\Ip('192.167.0.1');
		$this->assertFalse($ip->isPrivate(), 'Basic IPv4 address did not identify as public');

		$ip = new \Hubzero\Utility\Ip('172.15.255.255');
		$this->assertFalse($ip->isPrivate(), 'Basic IPv4 address did not identify as public');

		$ip = new \Hubzero\Utility\Ip('11.5.20.135');
		$this->assertFalse($ip->isPrivate(), 'Basic IPv4 address did not identify as public');
	}

	/**
	 * Tests to make sure bad arguments are caught
	 *
	 * @expectedException RuntimeException
	 * @return  void
	 **/
	public function testInvalidArgumentThrowsException()
	{
		$ip = new \Hubzero\Utility\Ip('172.16.0.1');
		$ip->isBetween('rock', 'hard place');
	}
}
