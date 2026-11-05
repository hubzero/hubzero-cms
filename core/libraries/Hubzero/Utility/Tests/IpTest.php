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
		$this->expectException(\RuntimeException::class);
		$ip->isBetween('rock', 'hard place');
	}

	/**
	 * Tests private IPv6 and IPv4-mapped addresses
	 *
	 * @return  void
	 **/
	public function testIsPrivateIpv6()
	{
		$ip = new \Hubzero\Utility\Ip('fd12:3456:789a::1');
		$this->assertTrue($ip->isPrivate(), 'Unique local IPv6 address did not identify as private');

		$ip = new \Hubzero\Utility\Ip('::ffff:10.1.2.3');
		$this->assertTrue($ip->isPrivate(), 'IPv4-mapped private address did not identify as private');

		$ip = new \Hubzero\Utility\Ip('2001:18e8:804:145::200');
		$this->assertFalse($ip->isPrivate(), 'Global IPv6 address identified as private');
	}

	/**
	 * Tests ranges of either family
	 *
	 * @return  void
	 **/
	public function testIsBetween()
	{
		$ip = new \Hubzero\Utility\Ip('192.0.2.7');
		$this->assertTrue($ip->isBetween('192.0.2.0', '192.0.2.255'));
		$this->assertFalse($ip->isBetween('192.0.3.0', '192.0.3.255'));
		$this->assertTrue($ip->isAbove('0.0.0.0'), 'A threshold of 0.0.0.0 is an address');

		$ip = new \Hubzero\Utility\Ip('2001:db8::8000');
		$this->assertTrue($ip->isBetween('2001:db8::', '2001:db8::ffff'));
		$this->assertFalse($ip->isBetween('2001:db8::1:0', '2001:db8::1:ffff'));

		// Families do not compare; a mapped address is IPv4
		$this->assertFalse($ip->isBetween('0.0.0.0', '255.255.255.255'));
		$ip = new \Hubzero\Utility\Ip('::ffff:192.0.2.7');
		$this->assertTrue($ip->isBetween('192.0.2.0', '192.0.2.255'));
	}

	/**
	 * Tests the canonical text form
	 *
	 * @return  void
	 **/
	public function testNormalize()
	{
		$this->assertEquals('192.0.2.7', \Hubzero\Utility\Ip::normalize('::ffff:192.0.2.7'));
		$this->assertEquals('192.0.2.7', \Hubzero\Utility\Ip::normalize('::FFFF:c000:207'));
		$this->assertEquals('2001:db8::1', \Hubzero\Utility\Ip::normalize('2001:0DB8:0000:0000:0000:0000:0000:0001'));
		$this->assertEquals('2001:db8::1', \Hubzero\Utility\Ip::normalize(' 2001:db8::1 '));
		$this->assertEquals('', \Hubzero\Utility\Ip::normalize('not an address'));
		$this->assertEquals('', \Hubzero\Utility\Ip::normalize(''));
	}

	/**
	 * Tests 16-byte packing, which must sort as the addresses do
	 *
	 * @return  void
	 **/
	public function testPacked16()
	{
		$v4 = \Hubzero\Utility\Ip::toPacked16('192.0.2.7');
		$this->assertEquals('00000000000000000000ffffc0000207', bin2hex($v4));
		$this->assertEquals($v4, \Hubzero\Utility\Ip::toPacked16('::ffff:192.0.2.7'));
		$this->assertEquals('192.0.2.7', \Hubzero\Utility\Ip::fromPacked16($v4));

		$v6 = \Hubzero\Utility\Ip::toPacked16('2001:db8::1');
		$this->assertEquals(16, strlen($v6));
		$this->assertEquals('2001:db8::1', \Hubzero\Utility\Ip::fromPacked16($v6));

		$this->assertTrue(\Hubzero\Utility\Ip::toPacked16('192.0.2.7') < \Hubzero\Utility\Ip::toPacked16('192.0.2.10'));
		$this->assertTrue(\Hubzero\Utility\Ip::toPacked16('255.255.255.255') < \Hubzero\Utility\Ip::toPacked16('2001:db8::'));
		$this->assertFalse(\Hubzero\Utility\Ip::toPacked16('nonsense'));
		$this->assertEquals('', \Hubzero\Utility\Ip::fromPacked16('abc'));
	}

	/**
	 * Tests reading an address, and telling an IPv4-mapped one
	 *
	 * @return  void
	 **/
	public function testToPacked()
	{
		$this->assertEquals('c0000207', bin2hex(\Hubzero\Utility\Ip::toPacked('192.0.2.7', $mapped)));
		$this->assertFalse($mapped);

		$this->assertEquals('c0000207', bin2hex(\Hubzero\Utility\Ip::toPacked('::ffff:192.0.2.7', $mapped)));
		$this->assertTrue($mapped);

		$this->assertEquals(16, strlen(\Hubzero\Utility\Ip::toPacked('2001:db8::1', $mapped)));
		$this->assertFalse($mapped);

		$this->assertFalse(\Hubzero\Utility\Ip::toPacked('nonsense', $mapped));
		$this->assertFalse($mapped);
	}

	/**
	 * Tests addresses as decimal numbers
	 *
	 * @return  void
	 **/
	public function testToDecimal()
	{
		$this->assertEquals('0', \Hubzero\Utility\Ip::toDecimal('::'));
		$this->assertEquals('0', \Hubzero\Utility\Ip::toDecimal('0.0.0.0'));
		$this->assertEquals('1', \Hubzero\Utility\Ip::toDecimal('::1'));
		$this->assertEquals('16909060', \Hubzero\Utility\Ip::toDecimal('1.2.3.4'));
		$this->assertEquals('16909060', \Hubzero\Utility\Ip::toDecimal('::ffff:1.2.3.4'));
		$this->assertEquals('4294967295', \Hubzero\Utility\Ip::toDecimal('255.255.255.255'));
		$this->assertEquals('1000000000', \Hubzero\Utility\Ip::toDecimal('::3b9a:ca00'));
		$this->assertEquals('42540766411282592856903984951653826561', \Hubzero\Utility\Ip::toDecimal('2001:db8::1'));
		$this->assertEquals('42540993323220755487007895883628937216', \Hubzero\Utility\Ip::toDecimal('2001:18e8:804:145::'));
		$this->assertEquals('340282366920938463463374607431768211455', \Hubzero\Utility\Ip::toDecimal('ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff'));
		$this->assertFalse(\Hubzero\Utility\Ip::toDecimal('nonsense'));
	}

	/**
	 * Tests which addresses an outbound request may go to
	 *
	 * @return  void
	 **/
	public function testIsPublic()
	{
		$this->assertTrue(\Hubzero\Utility\Ip::isPublic('128.46.19.124'));
		$this->assertTrue(\Hubzero\Utility\Ip::isPublic('2001:18e8:804:145::200'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('10.0.0.1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('::1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('fd00::1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('::ffff:127.0.0.1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('::ffff:169.254.169.254'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('64:ff9b::a00:1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('100::1'));
		// 6to4 for 127.0.0.1, Teredo, and IPv4-compatible ::127.0.0.1
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('2002:7f00:1::'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('2001:0:4136:e378:8000:63bf:3fff:fdd2'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('::7f00:1'));
		// Shared address space (a cloud metadata service is in it), protocol
		// assignments, benchmarking, multicast, local-use NAT64, site-local
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('100.100.100.200'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('192.0.0.170'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('198.18.0.1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('224.0.0.1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('ff02::1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('64:ff9b:1::a00:1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('fec0::1'));
		$this->assertFalse(\Hubzero\Utility\Ip::isPublic('nonsense'));
	}

	/**
	 * Tests that an address literal resolves to itself
	 *
	 * @return  void
	 **/
	public function testResolveLiteral()
	{
		$this->assertEquals(array('2001:db8::1'), \Hubzero\Utility\Ip::resolve('[2001:DB8::1]'));
		$this->assertEquals(array('192.0.2.7'), \Hubzero\Utility\Ip::resolve('192.0.2.7'));
		$this->assertEquals(array(), \Hubzero\Utility\Ip::resolve(''));
	}
}
