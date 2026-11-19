<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use PHPUnit\Framework\TestCase;

/**
 * How PUCAS logins and account linking find, create and claim auth_link rows
 *
 * A row with no user_id used to block linking its career account for good:
 * link() refused it as "already linked", and administrator logins kept making
 * such rows. Runs the plugin's lookups against an SQLite database; phpCAS is
 * not involved.
 */
final class PucasLinkTest extends TestCase
{
	/**
	 * @var  \Hubzero\Database\Driver
	 */
	private $db;

	/**
	 * Login options the administrator's com_login passes
	 *
	 * @var  array
	 */
	private $admin = array('authenticator' => 'pucas', 'autoregister' => false, 'action' => 'core.login.admin');

	/**
	 * Login options the frontend passes
	 *
	 * @var  array
	 */
	private $site = array('authenticator' => 'pucas', 'action' => 'core.login.site');

	protected function setUp(): void
	{
		require_once __DIR__ . '/fixtures.php';
	}

	private function plugin($client = 'administrator')
	{
		$app = PucasTestFixture::boot($client);
		$this->db = $app['db'];
		return PucasTestFixture::plugin($app);
	}

	private function linkCount()
	{
		$this->db->setQuery('SELECT COUNT(*) FROM jos_auth_link');
		return (int) $this->db->loadResult();
	}

	private function owner($username)
	{
		$this->db->setQuery("SELECT user_id FROM jos_auth_link WHERE username = '" . $username . "'");
		$owner = $this->db->loadResult();
		return ($owner === null) ? null : (int) $owner;
	}

	public function testAdminLoginFindsAnOwnedLink()
	{
		$hzal = $this->plugin()->findLink('owned', $this->admin);

		$this->assertSame(100, (int) $hzal->get('user_id'));
	}

	/**
	 * The case that left row after row behind: registration is allowed on the
	 * frontend, but an administrator login must not create a row
	 */
	public function testAdminLoginOfAnUnknownCareerAccountCreatesNoLink()
	{
		$plugin = $this->plugin();
		$before = $this->linkCount();

		$this->assertFalse($plugin->findLink('stranger', $this->admin));
		$this->assertSame($before, $this->linkCount());
	}

	public function testAdminLoginFindsAnOrphanLinkWithoutAddingOne()
	{
		$plugin = $this->plugin();
		$before = $this->linkCount();

		$hzal = $plugin->findLink('orphan', $this->admin);

		$this->assertSame(2, (int) $hzal->get('id'));
		$this->assertSame($before, $this->linkCount());
	}

	/**
	 * With registration on, an account named after its career account, with
	 * its purdue.edu address, could already log in to the administrator
	 * without a link
	 */
	public function testAdminLoginStillCreatesALinkForAMatchingAccount()
	{
		$plugin = $this->plugin();
		$before = $this->linkCount();

		$hzal = $plugin->findLink('match1', $this->admin);

		$this->assertNotFalse($hzal);
		$this->assertSame($before + 1, $this->linkCount());
	}

	public function testConfirmedAccountMatchesItsCareerAccount()
	{
		$this->plugin();

		$this->assertSame(102, (int) PucasTestPlugin::matchingAccount('match1')->get('id'));
	}

	/**
	 * Anyone can register a career account's name with its purdue.edu address;
	 * until the address is confirmed, the CAS login must not land there
	 */
	public function testUnconfirmedAccountDoesNotMatchItsCareerAccount()
	{
		$this->plugin();

		$this->assertNull(PucasTestPlugin::matchingAccount('squat1'));
	}

	public function testAdminLoginCreatesNoLinkForAnUnconfirmedAccount()
	{
		$plugin = $this->plugin();
		$before = $this->linkCount();

		$this->assertFalse($plugin->findLink('squat1', $this->admin));
		$this->assertSame($before, $this->linkCount());
	}

	public function testSiteLoginStillCreatesALink()
	{
		$plugin = $this->plugin('site');
		$before = $this->linkCount();

		$hzal = $plugin->findLink('stranger', $this->site);

		$this->assertNotFalse($hzal);
		$this->assertSame($before + 1, $this->linkCount());
	}

	public function testSiteLoginCreatesNoLinkWhenRegistrationIsOff()
	{
		$app = PucasTestFixture::boot('site');
		$app['component'] = new PucasTestComponents(array('com_members' => array('allowUserRegistration' => 0)));
		$this->db = $app['db'];
		$before = $this->linkCount();

		$this->assertFalse(PucasTestFixture::plugin($app)->findLink('stranger', $this->site));
		$this->assertSame($before, $this->linkCount());
	}

	/**
	 * With registration off, a matching account without a link was never let
	 * in, on either side
	 */
	public function testMatchingAccountGetsNoLinkWhenRegistrationIsOff()
	{
		foreach (array('administrator' => $this->admin, 'site' => $this->site) as $client => $options)
		{
			$app = PucasTestFixture::boot($client);
			$app['component'] = new PucasTestComponents(array('com_members' => array('allowUserRegistration' => 0)));
			$this->db = $app['db'];
			$before = $this->linkCount();

			$this->assertFalse(PucasTestFixture::plugin($app)->findLink('match1', $options), $client);
			$this->assertSame($before, $this->linkCount(), $client);
		}
	}

	public function testLinkingClaimsAnOrphanLink()
	{
		$plugin = $this->plugin('site');

		$this->assertSame('linked', $plugin->claimLink('orphan', 101, 'orphan@purdue.edu'));
		$this->assertSame(101, $this->owner('orphan'));
	}

	/**
	 * Rows written while user_id defaulted to 0 belong to nobody as well
	 */
	public function testLinkingClaimsALinkOwnedByUserZero()
	{
		$plugin = $this->plugin('site');

		$this->assertSame('linked', $plugin->claimLink('orphan0', 101, null));
		$this->assertSame(101, $this->owner('orphan0'));
	}

	public function testRelinkingYourOwnCareerAccountIsNotAnError()
	{
		$plugin = $this->plugin('site');
		$before = $this->linkCount();

		$this->assertSame('linked', $plugin->claimLink('owned', 100, 'owned@purdue.edu'));
		$this->assertSame(100, $this->owner('owned'));
		$this->assertSame($before, $this->linkCount());
	}

	public function testLinkingSomeoneElsesCareerAccountIsRefused()
	{
		$plugin = $this->plugin('site');

		$this->assertSame('taken', $plugin->claimLink('members', 100, 'members@purdue.edu'));
		$this->assertSame(101, $this->owner('members'));
	}

	public function testLinkingANewCareerAccountCreatesTheLink()
	{
		$plugin = $this->plugin('site');
		$before = $this->linkCount();

		$this->assertSame('linked', $plugin->claimLink('newcomer', 101, 'newcomer@purdue.edu'));
		$this->assertSame(101, $this->owner('newcomer'));
		$this->assertSame($before + 1, $this->linkCount());
	}
}
