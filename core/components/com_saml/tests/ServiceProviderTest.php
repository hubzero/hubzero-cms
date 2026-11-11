<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

use Hubzero\Test\Basic;
use Components\Saml\Models\ServiceProvider;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Service Provider model test
 *
 * Covers the trust-store helpers that need no database connection.
 */
#[CoversMethod(ServiceProvider::class, 'normalizeCertificate')]
#[CoversMethod(ServiceProvider::class, 'allowedGroups')]
#[CoversMethod(ServiceProvider::class, 'userCanAccess')]
#[CoversMethod(ServiceProvider::class, 'releasedGroups')]
class ServiceProviderTest extends Basic
{
    /**
     * PEM armor and whitespace are stripped down to the base64 body
     *
     * @return  void
     **/
    public function testNormalizeCertificate()
    {
        $body = 'MIIEMDCCAxigAwIBAgIJAPBXgeztn8U2';

        $pem = "-----BEGIN CERTIFICATE-----\n"
            . "MIIEMDCCAxigAwIBAgIJ\nAPBXgeztn8U2\n"
            . "-----END CERTIFICATE-----\n";

        $this->assertEquals($body, ServiceProvider::normalizeCertificate($pem));
        $this->assertEquals($body, ServiceProvider::normalizeCertificate($body));
        $this->assertEquals($body, ServiceProvider::normalizeCertificate("  MIIEMDCCAxigAwIBAgIJ \n\t APBXgeztn8U2  "));
        $this->assertEquals('', ServiceProvider::normalizeCertificate(''));
    }

    /**
     * A group restriction that cannot be read must deny, not allow
     *
     * @return  void
     **/
    public function testAllowedGroupsFailsClosed()
    {
        // No restriction configured — null means "any hub user"
        $this->assertNull(ServiceProvider::blank()->set('allowed_groups', null)->allowedGroups());
        $this->assertNull(ServiceProvider::blank()->set('allowed_groups', '')->allowedGroups());

        // Configured and readable
        $this->assertEquals(
            ['staff'],
            ServiceProvider::blank()->set('allowed_groups', '["staff"]')->allowedGroups(),
        );

        // Configured but unreadable — an empty list matches nobody, so a
        // corrupted restriction denies rather than opening the SP up
        $this->assertEquals(
            [],
            ServiceProvider::blank()->set('allowed_groups', 'not json')->allowedGroups(),
        );
        $this->assertEquals(
            [],
            ServiceProvider::blank()->set('allowed_groups', '["  "]')->allowedGroups(),
        );
    }

    /**
     * An SP with no group restriction admits any signed-in user
     *
     * @return  void
     **/
    public function testUnrestrictedSpAdmitsAnyUser()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', null);

        $this->assertTrue($sp->userCanAccess(new StubUser(42)));
    }

    /**
     * A guest (no user id) is never admitted to a restricted SP
     *
     * @return  void
     **/
    public function testGuestNeverAdmittedToRestrictedSp()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff"]');
        $sp->groups = ['staff' => [42]];

        $this->assertFalse($sp->userCanAccess(new StubUser(0)));
        $this->assertFalse($sp->userCanAccess(null));
    }

    /**
     * Membership of any one listed group is enough
     *
     * @return  void
     **/
    public function testMemberOfAnyListedGroupIsAdmitted()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff","faculty"]');
        $sp->groups = ['staff' => [7], 'faculty' => [42]];

        // Member of the second group only
        $this->assertTrue($sp->userCanAccess(new StubUser(42)));
        // Member of the first group only
        $this->assertTrue($sp->userCanAccess(new StubUser(7)));
        // Member of neither
        $this->assertFalse($sp->userCanAccess(new StubUser(99)));
    }

    /**
     * A restriction naming only groups that do not resolve admits nobody,
     * rather than falling open
     *
     * @return  void
     **/
    public function testUnresolvableGroupsAdmitNobody()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', '["deleted-group"]');
        $sp->groups = [];

        $this->assertFalse($sp->userCanAccess(new StubUser(42)));
    }

    /**
     * A corrupted restriction denies rather than opening the SP up
     *
     * @return  void
     **/
    public function testCorruptRestrictionFailsClosed()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', 'not json');
        $sp->groups = ['staff' => [42]];

        $this->assertFalse($sp->userCanAccess(new StubUser(42)));
    }

    /**
     * The 'allowed' policy releases the gates the user came through, and
     * only those — not the rest of the restriction, and not other groups
     *
     * @return  void
     **/
    public function testAllowedPolicyReleasesTheGatesPassed()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff","faculty"]');
        $sp->groups = ['staff' => [42], 'faculty' => [7, 42]];
        $sp->confirmed = ['staff', 'faculty', 'knitting-club'];

        $this->assertEquals(['staff', 'faculty'], $sp->releasedGroups(new StubUser(42)));
        $this->assertEquals(['faculty'], $sp->releasedGroups(new StubUser(7)));
        $this->assertEquals([], $sp->releasedGroups(new StubUser(99)));
    }

    /**
     * With no restriction there is no gate to report, so the 'allowed'
     * policy releases nothing rather than falling back to everything
     *
     * @return  void
     **/
    public function testAllowedPolicyReleasesNothingWithoutRestriction()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', null);
        $sp->confirmed = ['staff'];

        $this->assertEquals([], $sp->releasedGroups(new StubUser(42)));

        // A restriction that cannot be read must not open this up either
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', 'not json');
        $sp->confirmed = ['staff'];

        $this->assertEquals([], $sp->releasedGroups(new StubUser(42)));
    }

    /**
     * The 'all' policy releases confirmed membership, independently of the
     * restriction
     *
     * @return  void
     **/
    public function testAllPolicyReleasesConfirmedMembership()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff"]');
        $sp->confirmed = ['staff', 'knitting-club'];

        $this->assertEquals(['staff', 'knitting-club'], $sp->releasedGroups(new StubUser(42), 'all'));
    }

    /**
     * A guest has no membership to release under either policy
     *
     * @return  void
     **/
    public function testGuestReleasesNoGroups()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff"]');
        $sp->groups = ['staff' => [0]];
        $sp->confirmed = ['staff'];

        $this->assertEquals([], $sp->releasedGroups(new StubUser(0)));
        $this->assertEquals([], $sp->releasedGroups(new StubUser(0), 'all'));
        $this->assertEquals([], $sp->releasedGroups(null));
    }

    /**
     * A gate is released by the group's own cn, however the entry was
     * spelled, and two entries for one group release it once
     *
     * @return  void
     **/
    public function testAllowedPolicyReleasesTheGroupsOwnCn()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', '["1234","Staff","staff"]');
        $sp->groups = ['1234' => [42], 'Staff' => [42], 'staff' => [42]];
        $sp->canonical = ['1234' => 'faculty', 'Staff' => 'staff'];

        $this->assertEquals(['faculty', 'staff'], $sp->releasedGroups(new StubUser(42)));
    }

    /**
     * The access check and the release ask about each group once
     *
     * @return  void
     **/
    public function testMembershipIsLookedUpOncePerGroup()
    {
        $sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff","faculty"]');
        $sp->groups = ['faculty' => [42]];
        $user = new StubUser(42);

        $this->assertTrue($sp->userCanAccess($user));
        $sp->releasedGroups($user);
        $sp->releasedGroups($user);

        $this->assertEquals(2, $sp->lookups);
    }
}
