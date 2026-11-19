<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Helpers\Tests;

use Components\Members\Helpers\ExistingAccount;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/existingaccount.php';
require_once dirname(__DIR__, 2) . '/site/router.php';

/**
 * The Update Account form's pointer from a third-party login to the
 * account its owner already has
 */
final class ExistingAccountTest extends TestCase
{
	/**
	 * The auth domain of the login in these tests
	 */
	const DOMAIN = 5;

	protected function setUp(): void
	{
		$this->boot(0);
	}

	protected function tearDown(): void
	{
		\Hubzero\Database\Relational::setDefaultConnection(null);
	}

	/**
	 * @param  integer  $allowDuplicateEmails  com_members' allow_duplicate_emails
	 */
	private function boot($allowDuplicateEmails)
	{
		$app = new \Hubzero\Base\Application('site');
		$app['client']     = new \Hubzero\Base\Client\Site();
		$app['config']     = new \Hubzero\Config\Registry(array('secret' => 'test', 'cachetime' => 0));
		$app['dispatcher'] = new \Hubzero\Events\Dispatcher();
		$app['language']   = new \Hubzero\Test\Stubs\TranslatorStub();
		$app['router']     = new ExistingAccountTestRouter();
		$app['user']       = new ExistingAccountTestGuest();
		$app['request']    = new \Hubzero\Http\Request();
		$app['component']  = new ExistingAccountTestComponents(array(
			'com_members' => array('allow_duplicate_emails' => $allowDuplicateEmails)
		));
		$app['plugin']     = new ExistingAccountTestPlugins(array(
			'named'   => array('display_name' => 'Example University'),
			'unnamed' => array(),
		));
		$app['db'] = self::database();

		\Hubzero\Facades\Facade::setApplication($app);

		// Earlier tests in the suite (com_blog) leave a mock driver as the
		// models' default connection
		\Hubzero\Database\Relational::setDefaultConnection($app['db']);
	}

	private static function database()
	{
		$db = new \Hubzero\Database\Driver\Sqlite(array(
			'database' => ':memory:',
			'prefix'   => 'jos_',
			'user'     => '',
			'password' => ''
		));

		$statements = array(
			'CREATE TABLE jos_users (id INTEGER PRIMARY KEY, name TEXT, username TEXT, email TEXT,
				block INTEGER DEFAULT 0, activation INTEGER DEFAULT 1)',
			'CREATE TABLE jos_auth_link (id INTEGER PRIMARY KEY, auth_domain_id INTEGER, username TEXT, email TEXT,
				password TEXT, params TEXT, user_id INTEGER DEFAULT NULL, linked_on TEXT)',
			// 300 is the session's placeholder account; 103 never confirmed its address;
			// 104 already has a link with this provider
			"INSERT INTO jos_users (id, name, username, email, block, activation) VALUES
				(100, 'A Member', 'member1', 'member1@example.edu', 0, 1),
				(101, 'A Blocked Member', 'blocked1', 'blocked1@example.edu', 1, 1),
				(103, 'Unconfirmed', 'unconf1', 'unconf1@example.edu', 0, -4242),
				(104, 'Linked', 'linked1', 'linked1@example.edu', 0, 1),
				(300, 'Someone New', '-12', '-12@invalid', 0, 1)",
			"INSERT INTO jos_auth_link (id, auth_domain_id, username, email, user_id) VALUES
				(7, " . self::DOMAIN . ", 'other-identity', 'linked1@example.edu', 104),
				(12, " . self::DOMAIN . ", 'new-identity', 'member1@example.edu', 300)",
		);

		foreach ($statements as $sql)
		{
			$db->setQuery($sql);
			if (!$db->query())
			{
				throw new \RuntimeException('Fixture failed: ' . $sql);
			}
		}

		return $db;
	}

	public function testTheProvidersAddressMatchesTheAccountThatHasIt()
	{
		$this->assertTrue(ExistingAccount::matches('member1@example.edu', 300, self::DOMAIN));
	}

	public function testTheAccountItselfIsNotAMatch()
	{
		$this->assertFalse(ExistingAccount::matches('member1@example.edu', 100, self::DOMAIN));
	}

	public function testABlockedAccountIsNotAMatch()
	{
		$this->assertFalse(ExistingAccount::matches('blocked1@example.edu', 300, self::DOMAIN));
	}

	/**
	 * Anyone can register an address without confirming it; that account may
	 * not belong to the person this login asserts the address for
	 */
	public function testAnUnconfirmedAccountIsNotAMatch()
	{
		$this->assertFalse(ExistingAccount::matches('unconf1@example.edu', 300, self::DOMAIN));
	}

	/**
	 * Account settings only offers providers the account has not linked, so
	 * "link it there" would send the person to a choice that is not listed
	 */
	public function testAnAccountAlreadyLinkedWithThisProviderIsNotAMatch()
	{
		$this->assertFalse(ExistingAccount::matches('linked1@example.edu', 300, self::DOMAIN));
		$this->assertTrue(ExistingAccount::matches('linked1@example.edu', 300, self::DOMAIN + 1));
	}

	public function testAnUnknownAddressIsNotAMatch()
	{
		$this->assertFalse(ExistingAccount::matches('stranger@example.edu', 300, self::DOMAIN));
	}

	/**
	 * ORCID sends no address; placeholders carry "-N@invalid"
	 */
	public function testNoAddressOrAPlaceholderAddressIsNotAMatch()
	{
		$this->assertFalse(ExistingAccount::matches('', 300, self::DOMAIN));
		$this->assertFalse(ExistingAccount::matches(null, 300, self::DOMAIN));
		$this->assertFalse(ExistingAccount::matches('-12@invalid', 100, self::DOMAIN));
	}

	/**
	 * With duplicates allowed, the form does not say whether an address is in
	 * use, and the notice must not say it either
	 */
	public function testNothingMatchesWhenDuplicateAddressesAreAllowed()
	{
		$this->boot(1);
		$this->assertFalse(ExistingAccount::matches('member1@example.edu', 300, self::DOMAIN));

		$this->boot(2);
		$this->assertTrue(ExistingAccount::matches('member1@example.edu', 300, self::DOMAIN));
	}

	/**
	 * Log out (naming the provider), sign in, then Account settings, and
	 * nothing in between that the sign-in page would refuse as a return
	 */
	public function testSignInUrlLogsOutThenSignsInThenOpensAccountSettings()
	{
		$logout = ExistingAccount::signInUrl('shibboleth');
		parse_str((string) parse_url($logout, PHP_URL_QUERY), $query);
		$this->assertSame('com_users', $query['option']);
		$this->assertSame('logout', $query['task']);
		$this->assertSame('shibboleth', $query['authenticator']);

		$login = base64_decode($query['return'], true);
		parse_str((string) parse_url($login, PHP_URL_QUERY), $query);
		$this->assertSame('com_users', $query['option']);
		$this->assertSame('login', $query['view']);

		$account = base64_decode($query['return'], true);
		parse_str((string) parse_url($account, PHP_URL_QUERY), $query);
		$this->assertSame('com_members', $query['option']);
		$this->assertSame('myaccount', $query['task']);
		$this->assertSame('account', $query['active']);

		// The rule com_users\Controllers\Auth::displayTask() drops returns by
		$path   = (string) parse_url($account, PHP_URL_PATH);
		$rquery = (string) parse_url($account, PHP_URL_QUERY);
		$this->assertDoesNotMatchRegularExpression('#^/?(login|register)/?$#i', $path);
		$namesLoginView = preg_match('#(^|&)view=(login|register)(&|$)#i', $rquery);
		$this->assertFalse($namesLoginView && !preg_match('#(^|&)task=#i', $rquery));
	}

	/**
	 * Base64 "+" and "/" must survive the query string
	 */
	public function testSignInUrlEncodesTheNestedReturns()
	{
		$logout = ExistingAccount::signInUrl('a+b');
		$this->assertStringContainsString('authenticator=a%2Bb', $logout);
		$this->assertDoesNotMatchRegularExpression('#return=[^&]*[+/=]#', $logout);
	}

	/**
	 * What signInUrl() appends to the routed myaccount URL, checked against
	 * the members router itself: task=myaccount&active=account would build
	 * /members/account/myaccount, which parses as the member "account"
	 */
	public function testTheMembersRouterReadsTheAccountSettingsPath()
	{
		$router = new \Components\Members\Site\Router();

		$query = array('task' => 'myaccount');
		$this->assertSame(array('myaccount'), $router->build($query));

		$query = array('task' => 'myaccount', 'active' => 'account');
		$this->assertSame(array('account', 'myaccount'), $router->build($query));

		$segments = array('myaccount', 'account');
		$vars = $router->parse($segments);
		$this->assertSame('account', $vars['active']);
		$this->assertArrayNotHasKey('id', $vars);
	}

	/**
	 * On a hub with SEF URLs the section is appended to /members/myaccount
	 */
	public function testSignInUrlEndsAtTheAccountSettingsPathOnSefHubs()
	{
		\Hubzero\Facades\Facade::getApplication()['router']->sef = array(
			'index.php?option=com_members&task=myaccount' => '/members/myaccount'
		);

		parse_str((string) parse_url(ExistingAccount::signInUrl('shibboleth'), PHP_URL_QUERY), $query);
		parse_str((string) parse_url(base64_decode($query['return'], true), PHP_URL_QUERY), $query);

		$this->assertSame('/members/myaccount/account', base64_decode($query['return'], true));
	}

	public function testProviderNameComesFromTheDisplayNameParam()
	{
		$this->assertSame('Example University', ExistingAccount::providerName('named'));
	}

	public function testProviderNameFallsBackToThePluginName()
	{
		$this->assertSame('Unnamed', ExistingAccount::providerName('unnamed'));
	}
}

/**
 * Route::url() without the site router: returns the URL as given
 */
class ExistingAccountTestRouter
{
	/**
	 * URLs to answer with a SEF path, as the site router would
	 *
	 * @var  array
	 */
	public $sef = array();

	public function url($url, $xhtml = true, $ssl = null)
	{
		return isset($this->sef[$url]) ? $this->sef[$url] : $url;
	}
}

/**
 * A guest, for the members router's /members/myaccount branch
 */
class ExistingAccountTestGuest
{
	public function isGuest()
	{
		return true;
	}

	public function __call($name, $arguments)
	{
		return null;
	}
}

class ExistingAccountTestComponents
{
	private $params;

	public function __construct(array $params)
	{
		$this->params = $params;
	}

	public function params($option, $strict = false)
	{
		return new \Hubzero\Config\Registry(isset($this->params[$option]) ? $this->params[$option] : array());
	}

	public function __call($name, $arguments)
	{
		return null;
	}
}

/**
 * The authentication plugins and their params
 */
class ExistingAccountTestPlugins
{
	private $plugins;

	public function __construct(array $plugins)
	{
		$this->plugins = $plugins;
	}

	public function byType($type, $plugin = null)
	{
		if (!isset($this->plugins[$plugin]))
		{
			return null;
		}

		return (object) array('type' => $type, 'name' => $plugin, 'params' => json_encode((object) $this->plugins[$plugin]));
	}

	public function import($type, $plugin = null, $autocreate = true, $dispatcher = null)
	{
		return true;
	}

	public function __call($name, $arguments)
	{
		return null;
	}
}
