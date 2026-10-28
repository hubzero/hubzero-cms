<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

use Hubzero\Test\Basic;
use Components\Saml\Models\ServiceProvider;

require_once dirname(__DIR__) . '/models/ServiceProvider.php';

/**
 * Service Provider with the group lookup stubbed, so the access policy can
 * be exercised without a group table behind it.
 */
class StubbedServiceProvider extends ServiceProvider
{
	/**
	 * cn => list of member user IDs
	 *
	 * @var  array
	 */
	public $groups = array();

	/**
	 * @param   string   $cn
	 * @param   integer  $userId
	 * @return  boolean
	 */
	protected function userIsInGroup($cn, $userId)
	{
		$this->lookups++;

		return isset($this->groups[$cn]) && in_array($userId, $this->groups[$cn]);
	}

	/**
	 * Membership lookups made, to see they are not repeated
	 *
	 * @var  integer
	 */
	public $lookups = 0;

	/**
	 * Entry => the group's own cn; entries not listed are their own cn
	 *
	 * @var  array
	 */
	public $canonical = array();

	/**
	 * @param   string  $cn
	 * @return  string
	 */
	protected function groupCn($cn)
	{
		return isset($this->canonical[$cn]) ? $this->canonical[$cn] : $cn;
	}

	/**
	 * cn's this stub reports as confirmed membership
	 *
	 * @var  array
	 */
	public $confirmed = array();

	/**
	 * @param   integer  $userId
	 * @return  array
	 */
	protected function confirmedGroups($userId)
	{
		return $this->confirmed;
	}
}

/**
 * Stand-in for a user, which only needs to answer get('id') here
 */
class StubUser
{
	private $id;

	public function __construct($id)
	{
		$this->id = $id;
	}

	public function get($key, $default = null)
	{
		return $key == 'id' ? $this->id : $default;
	}
}

/**
 * Service Provider model test
 *
 * Covers the trust-store helpers that need no database connection.
 */
class ServiceProviderTest extends Basic
{
	/**
	 * PEM armor and whitespace are stripped down to the base64 body
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::normalizeCertificate
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
	 * @covers  Components\Saml\Models\ServiceProvider::allowedGroups
	 * @return  void
	 **/
	public function testAllowedGroupsFailsClosed()
	{
		// No restriction configured — null means "any hub user"
		$this->assertNull(ServiceProvider::blank()->set('allowed_groups', null)->allowedGroups());
		$this->assertNull(ServiceProvider::blank()->set('allowed_groups', '')->allowedGroups());

		// Configured and readable
		$this->assertEquals(
			array('staff'),
			ServiceProvider::blank()->set('allowed_groups', '["staff"]')->allowedGroups()
		);

		// Configured but unreadable — an empty list matches nobody, so a
		// corrupted restriction denies rather than opening the SP up
		$this->assertEquals(
			array(),
			ServiceProvider::blank()->set('allowed_groups', 'not json')->allowedGroups()
		);
		$this->assertEquals(
			array(),
			ServiceProvider::blank()->set('allowed_groups', '["  "]')->allowedGroups()
		);
	}

	/**
	 * An SP with no group restriction admits any signed-in user
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::userCanAccess
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
	 * @covers  Components\Saml\Models\ServiceProvider::userCanAccess
	 * @return  void
	 **/
	public function testGuestNeverAdmittedToRestrictedSp()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff"]');
		$sp->groups = array('staff' => array(42));

		$this->assertFalse($sp->userCanAccess(new StubUser(0)));
		$this->assertFalse($sp->userCanAccess(null));
	}

	/**
	 * Membership of any one listed group is enough
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::userCanAccess
	 * @return  void
	 **/
	public function testMemberOfAnyListedGroupIsAdmitted()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff","faculty"]');
		$sp->groups = array('staff' => array(7), 'faculty' => array(42));

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
	 * @covers  Components\Saml\Models\ServiceProvider::userCanAccess
	 * @return  void
	 **/
	public function testUnresolvableGroupsAdmitNobody()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', '["deleted-group"]');
		$sp->groups = array();

		$this->assertFalse($sp->userCanAccess(new StubUser(42)));
	}

	/**
	 * A corrupted restriction denies rather than opening the SP up
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::userCanAccess
	 * @return  void
	 **/
	public function testCorruptRestrictionFailsClosed()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', 'not json');
		$sp->groups = array('staff' => array(42));

		$this->assertFalse($sp->userCanAccess(new StubUser(42)));
	}

	/**
	 * The 'allowed' policy releases the gates the user came through, and
	 * only those — not the rest of the restriction, and not other groups
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::releasedGroups
	 * @return  void
	 **/
	public function testAllowedPolicyReleasesTheGatesPassed()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff","faculty"]');
		$sp->groups = array('staff' => array(42), 'faculty' => array(7, 42));
		$sp->confirmed = array('staff', 'faculty', 'knitting-club');

		$this->assertEquals(array('staff', 'faculty'), $sp->releasedGroups(new StubUser(42)));
		$this->assertEquals(array('faculty'), $sp->releasedGroups(new StubUser(7)));
		$this->assertEquals(array(), $sp->releasedGroups(new StubUser(99)));
	}

	/**
	 * With no restriction there is no gate to report, so the 'allowed'
	 * policy releases nothing rather than falling back to everything
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::releasedGroups
	 * @return  void
	 **/
	public function testAllowedPolicyReleasesNothingWithoutRestriction()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', null);
		$sp->confirmed = array('staff');

		$this->assertEquals(array(), $sp->releasedGroups(new StubUser(42)));

		// A restriction that cannot be read must not open this up either
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', 'not json');
		$sp->confirmed = array('staff');

		$this->assertEquals(array(), $sp->releasedGroups(new StubUser(42)));
	}

	/**
	 * The 'all' policy releases confirmed membership, independently of the
	 * restriction
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::releasedGroups
	 * @return  void
	 **/
	public function testAllPolicyReleasesConfirmedMembership()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff"]');
		$sp->confirmed = array('staff', 'knitting-club');

		$this->assertEquals(array('staff', 'knitting-club'), $sp->releasedGroups(new StubUser(42), 'all'));
	}

	/**
	 * A guest has no membership to release under either policy
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::releasedGroups
	 * @return  void
	 **/
	public function testGuestReleasesNoGroups()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff"]');
		$sp->groups = array('staff' => array(0));
		$sp->confirmed = array('staff');

		$this->assertEquals(array(), $sp->releasedGroups(new StubUser(0)));
		$this->assertEquals(array(), $sp->releasedGroups(new StubUser(0), 'all'));
		$this->assertEquals(array(), $sp->releasedGroups(null));
	}

	/**
	 * A gate is released by the group's own cn, however the entry was
	 * spelled, and two entries for one group release it once
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::releasedGroups
	 * @return  void
	 **/
	public function testAllowedPolicyReleasesTheGroupsOwnCn()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', '["1234","Staff","staff"]');
		$sp->groups = array('1234' => array(42), 'Staff' => array(42), 'staff' => array(42));
		$sp->canonical = array('1234' => 'faculty', 'Staff' => 'staff');

		$this->assertEquals(array('faculty', 'staff'), $sp->releasedGroups(new StubUser(42)));
	}

	/**
	 * The access check and the release ask about each group once
	 *
	 * @covers  Components\Saml\Models\ServiceProvider::releasedGroups
	 * @return  void
	 **/
	public function testMembershipIsLookedUpOncePerGroup()
	{
		$sp = StubbedServiceProvider::blank()->set('allowed_groups', '["staff","faculty"]');
		$sp->groups = array('faculty' => array(42));
		$user = new StubUser(42);

		$this->assertTrue($sp->userCanAccess($user));
		$sp->releasedGroups($user);
		$sp->releasedGroups($user);

		$this->assertEquals(2, $sp->lookups);
	}
}
