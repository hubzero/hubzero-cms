<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Components\Login\Admin\Controllers\Login as AdminLogin;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

/**
 * Shibboleth logins to the administrator, end to end
 *
 * Runs the administrator's com_login loginTask() with the mod_shib attributes of a
 * federated user, through the real Auth Manager, Guard, the Shibboleth plugin, the
 * hubzero user plugin and the ACL, against an SQLite database holding the default
 * Joomla groups and root asset rules (core.login.admin granted to Manager and below).
 * Only the plugin and component loaders, the session and the notifications are
 * stand-ins.
 *
 * Separate processes, because a successful login sets cookies, which PHP refuses
 * once PHPUnit has written to stdout.
 */
#[RunTestsInSeparateProcesses]
final class AdminLoginTest extends TestCase
{
	/**
	 * @var  ShibTestSession
	 */
	private $session;

	/**
	 * @var  ShibTestNotify
	 */
	private $notify;

	/**
	 * @var  \Hubzero\Database\Driver
	 */
	private $db;

	protected function setUp(): void
	{
		require_once __DIR__ . '/fixtures.php';

		$_SERVER['HTTP_HOST']   = 'hub.example.org';
		$_SERVER['SERVER_NAME'] = 'hub.example.org';
		$_SERVER['SCRIPT_NAME'] = '/administrator/index.php';
		$_SERVER['REQUEST_URI'] = '/administrator/login/shibboleth';
		$_GET = $_POST = $_REQUEST = $_COOKIE = array();
		$_GET['authenticator'] = $_REQUEST['authenticator'] = 'shibboleth';

		$app = ShibTestFixture::boot();
		$this->session = $app['session'];
		$this->notify  = $app['notification'];
		$this->db      = $app['db'];
	}

	/**
	 * Log in as the identity provider asserted $eppn, the way the rewrite of
	 * /administrator/login/shibboleth reaches com_login
	 *
	 * @param   string  $eppn
	 * @return  string  Where the controller redirected to
	 */
	private function shibbolethAdminLogin($eppn)
	{
		$_SERVER['Shib-Session-ID']        = '_session';
		$_SERVER['Shib-Identity-Provider'] = ShibTestFixture::IDP;
		$_SERVER['eppn']        = $eppn;
		$_SERVER['mail']        = $eppn;
		$_SERVER['displayName'] = 'Some Person';

		$controller = new AdminLogin();
		try
		{
			$controller->loginTask();
		}
		catch (ShibTestRedirect $e)
		{
			return $e->url;
		}
		$this->fail('loginTask() did not redirect');
	}

	/**
	 * The user the administrator session belongs to, or null for none
	 *
	 * @return  mixed
	 */
	private function sessionUser()
	{
		$user = $this->session->get('user');
		return ($user && !$user->get('guest')) ? $user->get('username') : null;
	}

	private function linkCount()
	{
		$this->db->setQuery('SELECT COUNT(*) FROM jos_auth_link');
		return (int) $this->db->loadResult();
	}

	/**
	 * Positive control: without it, the denials below could come from a broken fixture
	 */
	public function testAdministratorGetsAnAdministratorSession()
	{
		$this->shibbolethAdminLogin('admin1@example.edu');

		$this->assertSame('admin1', $this->sessionUser());
		$this->assertSame(array(), $this->notify->warnings);
		// The plugin's login() ran and stored the attributes
		$this->db->setQuery('SELECT COUNT(*) FROM jos_shibboleth_sessions');
		$this->assertSame(1, (int) $this->db->loadResult());
	}

	public function testRegisteredMemberIsDenied()
	{
		$this->shibbolethAdminLogin('member1@example.edu');

		$this->assertNull($this->sessionUser());
		$this->assertContains('JERROR_LOGIN_DENIED', $this->notify->warnings);
	}

	/**
	 * Registration is allowed on the frontend, but the administrator passes
	 * autoregister=false: no account and no auth_link row may come out of it
	 */
	public function testUnknownFederatedUserIsDeniedAndNoLinkIsCreated()
	{
		$before = $this->linkCount();

		$this->shibbolethAdminLogin('stranger@example.edu');

		$this->assertNull($this->sessionUser());
		$this->assertSame($before, $this->linkCount());
		$this->db->setQuery("SELECT COUNT(*) FROM jos_users WHERE username NOT IN ('admin1', 'member1')");
		$this->assertSame(0, (int) $this->db->loadResult());
		$this->assertNull($this->session->get('auth_link.tmp_username'));
	}

	/**
	 * A federated user whose link was created on the frontend but who never
	 * finished registering: the link has no account behind it
	 */
	public function testUnregisteredLinkIsDenied()
	{
		$this->db->setQuery("INSERT INTO jos_auth_link (auth_domain_id, username, user_id) VALUES (1, 'pending@example.edu', 0)");
		$this->db->query();

		$this->shibbolethAdminLogin('pending@example.edu');

		$this->assertNull($this->sessionUser());
		$this->db->setQuery("SELECT COUNT(*) FROM jos_users WHERE username NOT IN ('admin1', 'member1')");
		$this->assertSame(0, (int) $this->db->loadResult());
	}

	/**
	 * Only the identity provider's assertion counts: a username and password posted
	 * alongside, naming the administrator, must not log anybody in
	 */
	public function testPostedCredentialsDoNotOverrideTheAssertion()
	{
		$_POST['username'] = $_REQUEST['username'] = 'admin1';
		$_POST['passwd']   = $_REQUEST['passwd']   = 'anything';

		$this->shibbolethAdminLogin('member1@example.edu');

		$this->assertNull($this->sessionUser());
	}

	/**
	 * The frontend still creates the link for a new federated user (registration
	 * allowed, autoregister not set): only a caller that turns autoregister off,
	 * like the administrator, prevents it
	 */
	public function testFrontendOptionsStillCreateTheLink()
	{
		$plugin = new plgAuthenticationShibboleth(null, array('type' => 'authentication', 'name' => 'shibboleth', 'params' => '{"debug_enabled":0}'));
		$response = new \Hubzero\Auth\Response();
		$before = $this->linkCount();

		$plugin->onUserAuthenticate(array(), array('shibboleth' => array('idp' => ShibTestFixture::IDP, 'eppn' => 'stranger@example.edu', 'username' => 'stranger', 'displayName' => 'S')), $response);

		$this->assertSame(\Hubzero\Auth\Status::SUCCESS, $response->status);
		$this->assertSame($before + 1, $this->linkCount());
	}

	/**
	 * The picker carries mod_adminlogin's "&return=<base64>" to com_login, encoded so
	 * that a "+" in the base64 survives the query string
	 */
	public function testAdminPickerCarriesTheReturnUrl()
	{
		// Skips the reverse DNS lookup that picks a default institution
		$_COOKIE['shib-entity-id'] = ShibTestFixture::IDP;
		$return = base64_encode('/administrator/index.php?option=com_members&search=aa~');
		$this->assertStringContainsString('+', $return);

		$html = implode("\n", plgAuthenticationShibboleth::onRenderOption('&return=' . $return));

		$url = 'index.php?option=com_login&task=display&authenticator=shibboleth&return=' . rawurlencode($return) . '&idp=';
		$this->assertStringContainsString(' data-login-url="' . htmlspecialchars($url, ENT_QUOTES) . '"', $html);
		$this->assertStringContainsString('<a href="' . htmlspecialchars($url . rawurlencode(ShibTestFixture::IDP), ENT_QUOTES) . '">', $html);

		// The URL in the page, once the browser decodes it, carries the original base64
		$this->assertSame(1, preg_match('/ data-login-url="([^"]*)"/', $html, $m));
		parse_str(parse_url(html_entity_decode($m[1], ENT_QUOTES) . 'x', PHP_URL_QUERY), $query);
		$this->assertSame($return, $query['return']);
	}

	/**
	 * Anything but "&return=<base64>" is dropped rather than copied into the URL
	 */
	public function testAdminPickerDropsAMalformedReturn()
	{
		$_COOKIE['shib-entity-id'] = ShibTestFixture::IDP;

		$html = implode("\n", plgAuthenticationShibboleth::onRenderOption('&return=a"><script>&x=1'));

		$this->assertStringNotContainsString('return=', $html);
		$this->assertStringNotContainsString('<script>', $html);
		$this->assertStringContainsString(' data-login-url="index.php?option=com_login&amp;task=display&amp;authenticator=shibboleth&amp;idp="', $html);
	}

	/**
	 * When display() sends the browser back to the login page (here for an unknown
	 * identity provider), the return value is encoded, so a "+" survives
	 */
	public function testDisplayFailureRedirectKeepsTheReturnUrl()
	{
		$return = base64_encode('/administrator/index.php?option=com_members&search=aa~');
		$_GET['idp'] = 'https://unknown.example.org/idp';
		$view = new stdClass;
		$view->return = $return;
		$plugin = new plgAuthenticationShibboleth(null, array('type' => 'authentication', 'name' => 'shibboleth', 'params' => '{"debug_enabled":0}'));

		try
		{
			$plugin->display($view, null);
			$this->fail('display() did not redirect');
		}
		catch (ShibTestRedirect $e)
		{
			$this->assertStringEndsWith('&view=login&return=' . rawurlencode($return), $e->url);
			parse_str(parse_url($e->url, PHP_URL_QUERY), $query);
			$this->assertSame($return, $query['return']);
		}
	}

	/**
	 * With "Admin login" off, com_login does not run the plugin's login() at all, so
	 * nothing is stored for the identity provider's attributes, and nobody logs in
	 */
	public function testAdminLoginOffDoesNotRunThePlugin()
	{
		$app = ShibTestFixture::boot('administrator', array('admin_login' => 0));
		$this->session = $app['session'];
		$this->notify  = $app['notification'];
		$this->db      = $app['db'];

		$this->shibbolethAdminLogin('admin1@example.edu');

		$this->db->setQuery('SELECT COUNT(*) FROM jos_shibboleth_sessions');
		$this->assertSame(0, (int) $this->db->loadResult());
		$this->assertNull($this->sessionUser());
		$this->assertSame(array('Invalid authenticator'), $this->notify->errors);
	}
}
