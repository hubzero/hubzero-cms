<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility\Tests;

use Hubzero\Utility\Network;
use Hubzero\Test\Basic;

/**
 * Tests for IPv4 and IPv6 networks
 */
class NetworkTest extends Basic
{
    /**
     * An IPv4 network holds the addresses inside its prefix, and no others
     *
     * @return  void
     */
    public function testIpv4()
    {
        $net = Network::parse('192.0.2.0/24');

        $this->assertTrue($net->contains('192.0.2.0'));
        $this->assertTrue($net->contains('192.0.2.255'));
        $this->assertFalse($net->contains('192.0.3.0'));
        $this->assertTrue(Network::parse('10.0.0.0/8')->contains('10.255.1.2'));
        $this->assertTrue(Network::parse('192.0.2.77/24')->contains('192.0.2.1'));
        $this->assertTrue(Network::parse('192.0.2.7/32')->contains('192.0.2.7'));
        $this->assertFalse(Network::parse('192.0.2.7/32')->contains('192.0.2.8'));
    }

    /**
     * IPv6 networks work without a big-number library
     *
     * @return  void
     */
    public function testIpv6()
    {
        $net = Network::parse('2001:db8::/32');

        $this->assertTrue($net->contains('2001:db8:ffff::1'));
        $this->assertFalse($net->contains('2001:db9::1'));
        $this->assertTrue(Network::parse('2001:db8:1:2::/63')->contains('2001:db8:1:3::5'));
        $this->assertFalse(Network::parse('2001:db8:1:2::/64')->contains('2001:db8:1:3::5'));
    }

    /**
     * An address of one family is never in a network of the other
     *
     * @return  void
     */
    public function testFamiliesDoNotMix()
    {
        $this->assertFalse(Network::parse('2001:db8::/32')->contains('192.0.2.7'));
        $this->assertFalse(Network::parse('192.0.2.0/24')->contains('2001:db8::1'));
        // The IPv4-compatible form ::a.b.c.d is an IPv6 address
        $this->assertFalse(Network::parse('::/2')->contains('192.0.2.7'));
    }

    /**
     * An IPv4 visitor reported as an IPv4-mapped IPv6 address is still IPv4
     *
     * @return  void
     */
    public function testMappedAddressesAreIpv4()
    {
        $this->assertTrue(Network::parse('192.0.2.0/24')->contains('::ffff:192.0.2.7'));
        $this->assertTrue(Network::parse('::ffff:192.0.2.0/120')->contains('192.0.2.9'));
    }

    /**
     * A prefix of 0 is every address of either family
     *
     * @return  void
     */
    public function testPrefixZeroIsEveryone()
    {
        $this->assertTrue(Network::parse('0.0.0.0/0')->contains('192.0.2.7'));
        $this->assertTrue(Network::parse('0.0.0.0/0')->contains('2001:db8::1'));
        $this->assertTrue(Network::parse('::/0')->contains('192.0.2.7'));
        $this->assertTrue(Network::parse('0.0.0.0/0.0.0.0')->contains('2001:db8::1'));

        $this->assertTrue(Network::parse('0.0.0.0/0')->isEveryone());
        $this->assertTrue(Network::parse('::/0')->isEveryone());
        $this->assertFalse(Network::parse('::ffff:0:0/96')->isEveryone());
        $this->assertFalse(Network::parse('10.0.0.0/8')->isEveryone());
    }

    /**
     * ::ffff:0:0/96 is all of IPv4, and no IPv6 address
     *
     * @return  void
     */
    public function testMappedSpaceIsOnlyIpv4()
    {
        $net = Network::parse('::ffff:0:0/96');

        $this->assertTrue($net->contains('192.0.2.7'));
        $this->assertTrue($net->contains('::ffff:10.0.0.1'));
        $this->assertFalse($net->contains('2001:db8::1'));
        $this->assertFalse($net->contains('::1'));
    }

    /**
     * A dotted mask is a prefix, if its ones are all at the front
     *
     * @return  void
     */
    public function testDottedMasks()
    {
        $this->assertTrue(Network::parse('10.0.0.0/255.0.0.0')->contains('10.1.2.3'));
        $this->assertFalse(Network::parse('10.0.0.0/255.0.0.0')->contains('11.1.2.3'));
        $this->assertNull(Network::parse('10.0.0.0/255.0.255.0'));
    }

    /**
     * The bounds of a network are its lowest and highest addresses
     *
     * @return  void
     */
    public function testBounds()
    {
        $net = Network::parse('192.0.2.77/24');
        $this->assertEquals('192.0.2.0', $net->first());
        $this->assertEquals('192.0.2.255', $net->last());

        $net = Network::parse('2001:db8:1:2::/63');
        $this->assertEquals('2001:db8:1:2::', $net->first());
        $this->assertEquals('2001:db8:1:3:ffff:ffff:ffff:ffff', $net->last());

        $net = Network::parse('192.0.2.7');
        $this->assertEquals('192.0.2.7', $net->first());
        $this->assertEquals('192.0.2.7', $net->last());

        // A mapped network is an IPv4 one
        $net = Network::parse('::ffff:192.0.2.0/120');
        $this->assertEquals('192.0.2.0', $net->first());
        $this->assertEquals('192.0.2.255', $net->last());
    }

    /**
     * Anything that is not a network is null, and nothing is in it
     *
     * @return  void
     */
    public function testNonsenseIsNotANetwork()
    {
        foreach (['nonsense/24', '192.0.2.0/33', '2001:db8::/129', '192.0.2.0/-1', '192.0.2.0/x', ''] as $cidr)
        {
            $this->assertNull(Network::parse($cidr), $cidr);
        }

        $this->assertFalse(Network::parse('192.0.2.0/24')->contains('not an address'));
    }
}
