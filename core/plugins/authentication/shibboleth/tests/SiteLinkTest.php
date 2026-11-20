<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

/**
 * Linking an institutional login to a signed-in frontend account
 *
 * Account settings sends a signed-in member through the IdP; mod_shib brings
 * them back to /login/shibboleth, which runs login(). That must end at the
 * link task, and link() must link the sign-in that just happened, for this
 * member only:
 *
 * - not fall through to an ordinary login, which put a new placeholder
 *   account in place of the signed-in member;
 * - not take the sign-in from the shib-session cookie, which holds whoever
 *   last signed in with the browser;
 * - not take it from the request, where anyone could put their own.
 *
 * Separate processes, because login() sets cookies, which PHP refuses once
 * PHPUnit has written to stdout.
 */
#[RunTestsInSeparateProcesses]
final class SiteLinkTest extends TestCase
{
	/**
	 * @var  ShibTestSession
	 */
	private $session;

	/**
	 * @var  \Hubzero\Database\Driver
	 */
	private $db;

	protected function setUp(): void
	{
		require_once __DIR__ . '/fixtures.php';

		$_SERVER['HTTP_HOST']   = 'hub.example.org';
		$_SERVER['SERVER_NAME'] = 'hub.example.org';
		$_SERVER['SCRIPT_NAME'] = '/index.php';
		$_SERVER['REQUEST_URI'] = '/login/shibboleth';
		$_GET = $_POST = $_REQUEST = $_COOKIE = array();

		$app = ShibTestFixture::boot('site');
		$this->session = $app['session'];
		$this->db      = $app['db'];

		// An earlier institutional sign-in in this browser, by someone else
		$this->store('stale-key', array(
			'idp'   => ShibTestFixture::IDP,
			'eppn'  => 'someone-else@example.edu',
			'email' => 'someone-else@example.edu'
		));
	}

	/**
	 * Store attributes the way login() does
	 *
	 * @param  string  $key
	 * @param  array   $attrs
	 */
	private function store($key, array $attrs)
	{
		$this->db->setQuery('INSERT INTO jos_shibboleth_sessions (session_key, data) VALUES ('
			. $this->db->quote($key) . ', ' . $this->db->quote(json_encode($attrs)) . ')');
		$this->db->query();
	}

	/**
	 * @param  integer  $id  Account to sign in, as a password login would
	 */
	private function signIn($id)
	{
		$user = \Hubzero\User\User::oneOrFail($id);
		$user->set('guest', false);
		$this->session->set('user', $user);
	}

	/**
	 * What mod_shib sets on the return from the IdP
	 *
	 * @param  string  $eppn
	 */
	private function backFromIdp($eppn)
	{
		$_SERVER['Shib-Session-ID']        = '_session';
		$_SERVER['Shib-Identity-Provider'] = ShibTestFixture::IDP;
		$_SERVER['eppn']        = $eppn;
		$_SERVER['mail']        = $eppn;
		$_SERVER['displayName'] = 'Some Person';
	}

	private function plugin()
	{
		return new plgAuthenticationShibboleth(null, array(
			'type'   => 'authentication',
			'name'   => 'shibboleth',
			'params' => '{"debug_enabled":0}'
		));
	}

	/**
	 * Run login() and return where it redirected, or null when it returned
	 *
	 * @param   array  $options  Receives the options login() fills in
	 * @return  mixed
	 */
	private function login(&$options = array())
	{
		$credentials = array();
		$options     = array();

		try
		{
			$this->plugin()->login($credentials, $options);
		}
		catch (ShibTestRedirect $e)
		{
			return $e->url;
		}

		return null;
	}

	/**
	 * Run link() as com_users' link task does; null means it linked without
	 * complaint (the task then reports success)
	 *
	 * @return  mixed  ShibTestRedirect or null
	 */
	private function linkTask()
	{
		try
		{
			$this->plugin()->link();
		}
		catch (ShibTestRedirect $e)
		{
			return $e;
		}

		return null;
	}

	/**
	 * Sign in member 101, go through the IdP as $eppn, and follow the redirect
	 * to the link task, which the shib-session cookie (path /login) does not reach
	 *
	 * @param   string  $eppn
	 * @return  mixed   What linkTask() returns
	 */
	private function linkFromAccountSettings($eppn)
	{
		$this->signIn(101);
		$_COOKIE['shib-session'] = 'stale-key';
		$this->backFromIdp($eppn);

		$this->login();

		unset($_COOKIE['shib-session']);
		return $this->linkTask();
	}

	/**
	 * @param   string  $url
	 * @return  array   Its query
	 */
	private static function query($url)
	{
		parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
		return $query;
	}

	private function linkOwner($eppn)
	{
		$this->db->setQuery('SELECT user_id FROM jos_auth_link WHERE username = ' . $this->db->quote($eppn));
		$owner = $this->db->loadResult();
		return ($owner === null) ? null : (int) $owner;
	}

	private function rowCount($table, $where = '1')
	{
		$this->db->setQuery('SELECT COUNT(*) FROM ' . $table . ' WHERE ' . $where);
		return (int) $this->db->loadResult();
	}

	/**
	 * A member signed in with a password links an institutional login from
	 * Account settings; coming back from the IdP must lead to the link task
	 */
	public function testSignedInReturnFromTheIdpGoesToTheLinkTask()
	{
		$this->signIn(101);
		$_COOKIE['shib-session'] = 'stale-key';
		$this->backFromIdp('new-id@example.edu');

		$url = $this->login($options);

		$this->assertNotNull($url, 'login() must redirect to the link task, not go on to log in');
		$query = self::query($url);
		$this->assertSame('com_users', $query['option']);
		$this->assertSame('user.link', $query['task']);
		$this->assertSame('shibboleth', $query['authenticator']);
		$this->assertArrayNotHasKey('shib-session', $query, 'the key must not travel in the URL');

		// The member's session names the sign-in just stored
		$key = $this->session->get('shibboleth.link');
		$this->assertNotEmpty($key);
		$this->assertNotSame('stale-key', $key);
		$this->db->setQuery('SELECT data FROM jos_shibboleth_sessions WHERE session_key = ' . $this->db->quote($key));
		$stored = json_decode((string) $this->db->loadResult(), true);
		$this->assertSame('new-id@example.edu', $stored['eppn']);

		// Still the member: nothing logged anybody else in
		$this->assertSame(101, (int) $this->session->get('user')->get('id'));
	}

	public function testTheLinkTaskLinksTheNewSignIn()
	{
		$this->assertNull($this->linkFromAccountSettings('new-id@example.edu'));

		$this->assertSame(101, $this->linkOwner('new-id@example.edu'));
		$this->assertNull($this->linkOwner('someone-else@example.edu'));
	}

	/**
	 * The stored sign-in is used once and then removed, from the session and
	 * from the table
	 */
	public function testTheStoredSignInIsUsedOnlyOnce()
	{
		$this->linkFromAccountSettings('new-id@example.edu');

		$this->assertNull($this->session->get('shibboleth.link'));
		$this->assertSame(0, $this->rowCount('jos_shibboleth_sessions', "data LIKE '%new-id@example.edu%'"));

		$again = $this->linkTask();
		$this->assertInstanceOf(ShibTestRedirect::class, $again);
		$this->assertSame('error', $again->type);
	}

	/**
	 * A key in the request, or the shib-session cookie, never names the
	 * identity to link: someone could hand a signed-in member a link with
	 * their own key and take over the member's account
	 */
	public function testAKeyInTheRequestOrCookieIsNotLinked()
	{
		$this->signIn(101);
		$_GET['shib-session']    = 'stale-key';
		$_COOKIE['shib-session'] = 'stale-key';

		$redirect = $this->linkTask();

		$this->assertInstanceOf(ShibTestRedirect::class, $redirect);
		$this->assertSame('error', $redirect->type);
		$this->assertNull($this->linkOwner('someone-else@example.edu'));
	}

	/**
	 * Signed in but not back from the IdP: the sign-in stored earlier in this
	 * browser may be someone else's (a shared computer), so it is not reused
	 */
	public function testSignedInWithoutAnIdpSessionLinksNothing()
	{
		$this->signIn(101);
		$_COOKIE['shib-session'] = 'stale-key';
		$this->session->set('shibboleth.link', 'stale-key');

		$query = self::query($this->login());

		$this->assertSame('user.link', $query['task']);
		$this->assertArrayNotHasKey('shib-session', $query);
		$this->assertNull($this->session->get('shibboleth.link'));

		$redirect = $this->linkTask();
		$this->assertInstanceOf(ShibTestRedirect::class, $redirect);
		$this->assertSame('error', $redirect->type);
		$this->assertNull($this->linkOwner('someone-else@example.edu'));
	}

	/**
	 * A row for this identity with no account behind it (a login that never
	 * finished registering) is claimed, not reported as linked to someone
	 */
	public function testARowWithNoAccountIsClaimed()
	{
		$this->db->setQuery("INSERT INTO jos_auth_link (auth_domain_id, username, user_id) VALUES
			(1, 'new-id@example.edu', NULL), (1, 'zero-id@example.edu', 0)");
		$this->db->query();

		$this->assertNull($this->linkFromAccountSettings('new-id@example.edu'));
		$this->assertSame(101, $this->linkOwner('new-id@example.edu'));
		$this->assertSame(1, $this->rowCount('jos_auth_link', "username = 'new-id@example.edu'"));

		$this->assertNull($this->linkFromAccountSettings('zero-id@example.edu'));
		$this->assertSame(101, $this->linkOwner('zero-id@example.edu'));
	}

	public function testARowOwnedByAnotherAccountIsRefused()
	{
		$this->db->setQuery("INSERT INTO jos_auth_link (auth_domain_id, username, user_id)
			VALUES (1, 'new-id@example.edu', 100)");
		$this->db->query();

		$redirect = $this->linkFromAccountSettings('new-id@example.edu');

		$this->assertInstanceOf(ShibTestRedirect::class, $redirect);
		$this->assertSame('error', $redirect->type);
		$this->assertSame(100, $this->linkOwner('new-id@example.edu'));
	}

	public function testRelinkingYourOwnIdentityIsNotAnError()
	{
		$this->db->setQuery("INSERT INTO jos_auth_link (auth_domain_id, username, user_id)
			VALUES (1, 'new-id@example.edu', 101)");
		$this->db->query();

		$this->assertNull($this->linkFromAccountSettings('new-id@example.edu'));
		$this->assertSame(101, $this->linkOwner('new-id@example.edu'));
	}

	/**
	 * An IdP that releases no identifier: the link task must say it failed,
	 * not let com_users report success
	 */
	public function testASignInWithoutAnIdentifierIsReportedAsAFailure()
	{
		$this->signIn(101);
		$this->store('no-id', array('idp' => ShibTestFixture::IDP, 'eppn' => null, 'email' => 'x@example.edu'));
		$this->session->set('shibboleth.link', 'no-id');
		$before = $this->rowCount('jos_auth_link');

		$redirect = $this->linkTask();

		$this->assertInstanceOf(ShibTestRedirect::class, $redirect);
		$this->assertSame('error', $redirect->type);
		$this->assertSame($before, $this->rowCount('jos_auth_link'));
	}

	/**
	 * A guest coming back from the IdP still logs in: login() returns with the
	 * attributes for onUserAuthenticate()
	 */
	public function testGuestReturnFromTheIdpStillLogsIn()
	{
		$this->backFromIdp('member1@example.edu');

		$url = $this->login($options);

		$this->assertNull($url);
		$this->assertSame('member1@example.edu', $options['shibboleth']['eppn']);
		$this->assertSame(ShibTestFixture::IDP, $options['shibboleth']['idp']);
		$this->assertNull($this->session->get('shibboleth.link'));
	}
}
