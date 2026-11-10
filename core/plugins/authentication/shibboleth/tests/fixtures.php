<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Stand-ins for AdminLoginTest. Each covers only what the login path calls.
 */

require_once PATH_CORE . '/plugins/authentication/shibboleth/shibboleth.php';
// After every plugin test bootstrap, one of which requires this file without _once
require_once PATH_CORE . '/plugins/user/hubzero/hubzero.php';
require_once PATH_CORE . '/components/com_login/models/login.php';
require_once PATH_CORE . '/components/com_login/admin/controllers/login.php';

/**
 * The administrator application, with the database below and the stand-ins
 */
class ShibTestFixture
{
	const IDP = 'https://idp.example.edu/idp/shibboleth';

	/**
	 * @param   string  $client      'administrator' or 'site'
	 * @param   array   $shibboleth  Shibboleth plugin params that override the defaults
	 * @return  ShibTestApplication
	 */
	public static function boot($client = 'administrator', array $shibboleth = array())
	{
		$app = new ShibTestApplication($client);
		$app['client']       = ($client == 'site') ? new \Hubzero\Base\Client\Site() : new \Hubzero\Base\Client\Administrator();
		$app['config']       = new \Hubzero\Config\Registry(array('secret' => 'test', 'root_user' => '', 'session_handler' => 'none', 'cachetime' => 0));
		$app['dispatcher']   = new \Hubzero\Events\Dispatcher();
		$app['language']     = new \Hubzero\Test\Stubs\TranslatorStub();
		$app['session']      = new ShibTestSession();
		$app['notification'] = new ShibTestNotify();
		$app['router']       = new ShibTestRouter();
		$app['document']     = new ShibTestDocument();
		$app['template']     = (object) array('template' => 'kameleon', 'path' => PATH_CORE . '/templates/kameleon', 'protected' => 1);
		$app['component']    = new ShibTestComponents(array(
			'com_members' => array('allowUserRegistration' => 1, 'guest_usergroup' => 1, 'new_usertype' => 2)
		));
		$app['plugin']       = new ShibTestPlugins(array(
			'authentication' => array(
				'shibboleth' => array_merge(array(
					'admin_login'   => 1,
					'site_login'    => 1,
					'debug_enabled' => 0,
					// Stored JSON-encoded inside the params, as the plugin's admin form saves it
					'institutions'  => json_encode(array('activeIdps' => array(
						array('entity_id' => self::IDP, 'label' => 'Example University', 'host' => 'example.edu'),
					))),
				), $shibboleth),
			),
			'user' => array(
				'hubzero' => array(),
			),
		));
		$app['db']   = ShibTestSchema::create();
		$app['user'] = function ($app)
		{
			return new \Hubzero\User\Manager($app);
		};
		$app['auth'] = function ($app)
		{
			return new \Hubzero\Auth\Manager($app);
		};

		\Hubzero\Facades\Facade::setApplication($app);
		// The administrator has this alias; core/bootstrap/Cli/aliases.php does not
		\Hubzero\Facades\Facade::createAliases(array('Notify' => \Hubzero\Facades\Notify::class));

		// What Plugin::import('user') does in the application
		$app['dispatcher']->addListener(new plgUserHubzero($app['dispatcher'], array('type' => 'user', 'name' => 'hubzero', 'params' => '{}')));

		return $app;
	}
}

/**
 * Collects the assets onRenderOption() attaches
 */
class ShibTestDocument
{
	public function __call($name, $arguments)
	{
		return $this;
	}
}

class ShibTestRedirect extends Exception
{
	public $url;

	public function __construct($url)
	{
		parent::__construct('redirect to ' . $url);
		$this->url = $url;
	}
}

/**
 * The real application (isAdmin() comes from its client), except that a redirect
 * ends the request with an exception instead of sending headers and exiting
 */
class ShibTestApplication extends \Hubzero\Base\Application
{
	public function redirect($url, $message = null, $type = 'success')
	{
		throw new ShibTestRedirect((string) $url);
	}
}

class ShibTestSession
{
	private $data = array();

	public function get($key, $default = null)
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}

	public function set($key, $value)
	{
		$old = $this->get($key);
		$this->data[$key] = $value;
		return $old;
	}

	public function has($key)
	{
		return array_key_exists($key, $this->data);
	}

	public function clear($key)
	{
		unset($this->data[$key]);
	}

	public function isNew()
	{
		return false;
	}

	public function getId()
	{
		return 'test-session';
	}
}

/**
 * Records what Notify:: would have shown
 */
class ShibTestNotify
{
	public $warnings = array();
	public $errors = array();

	public function warning($message)
	{
		$this->warnings[] = $message;
	}

	public function error($message)
	{
		$this->errors[] = $message;
	}

	public function __call($name, $arguments)
	{
	}
}

class ShibTestRouter
{
	public function url($url, $xhtml = true)
	{
		return $url;
	}
}

class ShibTestComponents
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

	public function isEnabled($option, $strict = false)
	{
		return true;
	}

	public function __call($name, $arguments)
	{
		return null;
	}
}

/**
 * The enabled plugins and their params, as rows of jos_extensions
 */
class ShibTestPlugins
{
	private $plugins;

	public function __construct(array $plugins)
	{
		$this->plugins = $plugins;
	}

	public function import($type, $plugin = null, $autocreate = true, $dispatcher = null)
	{
		return true;
	}

	public function byType($type, $plugin = null)
	{
		$rows = array();
		foreach (isset($this->plugins[$type]) ? $this->plugins[$type] : array() as $name => $params)
		{
			if ($plugin && $plugin != $name)
			{
				continue;
			}
			$rows[] = (object) array('type' => $type, 'name' => $name, 'params' => json_encode((object) $params), 'protected' => 0);
		}
		return $plugin ? (isset($rows[0]) ? $rows[0] : null) : $rows;
	}

	public function params($type, $name)
	{
		return new \Hubzero\Config\Registry(isset($this->plugins[$type][$name]) ? $this->plugins[$type][$name] : array());
	}

	public function isEnabled($type, $plugin = null)
	{
		return $plugin ? isset($this->plugins[$type][$plugin]) : isset($this->plugins[$type]);
	}

	public function __call($name, $arguments)
	{
		return null;
	}
}

/**
 * An in-memory SQLite database with the default Joomla groups and root asset
 * rules, an administrator, a registered member and their Shibboleth links
 */
class ShibTestSchema
{
	public static function create()
	{
		$db = new \Hubzero\Database\Driver\Sqlite(array('database' => ':memory:', 'prefix' => 'jos_', 'user' => '', 'password' => ''));

		$statements = array(
			'CREATE TABLE jos_users (id INTEGER PRIMARY KEY, name TEXT, givenName TEXT, middleName TEXT, surname TEXT,
				username TEXT, email TEXT, password TEXT, usertype TEXT, block INTEGER DEFAULT 0, approved INTEGER DEFAULT 2,
				sendEmail INTEGER DEFAULT 0, activation INTEGER DEFAULT 1, registerDate TEXT, lastvisitDate TEXT,
				params TEXT, secret TEXT, homeDirectory TEXT, loginShell TEXT, ftpShell TEXT, access INTEGER DEFAULT 1,
				usageAgreement INTEGER DEFAULT 0, modifyDate TEXT, registerIP TEXT, lastresetTime TEXT, resetCount INTEGER DEFAULT 0)',
			'CREATE TABLE jos_usergroups (id INTEGER PRIMARY KEY, parent_id INTEGER, lft INTEGER, rgt INTEGER, title TEXT)',
			'CREATE TABLE jos_user_usergroup_map (user_id INTEGER, group_id INTEGER)',
			'CREATE TABLE jos_assets (id INTEGER PRIMARY KEY, parent_id INTEGER, lft INTEGER, rgt INTEGER, level INTEGER,
				name TEXT, title TEXT, rules TEXT)',
			'CREATE TABLE jos_auth_domain (id INTEGER PRIMARY KEY, type TEXT, authenticator TEXT, domain TEXT, params TEXT)',
			'CREATE TABLE jos_auth_link (id INTEGER PRIMARY KEY, auth_domain_id INTEGER, username TEXT, email TEXT,
				password TEXT, params TEXT, user_id INTEGER DEFAULT 0, linked_on TEXT, linked_user TEXT)',
			'CREATE TABLE jos_shibboleth_sessions (id INTEGER PRIMARY KEY, session_key TEXT, data TEXT)',
			'CREATE TABLE jos_users_password (id INTEGER PRIMARY KEY, user_id INTEGER, passhash TEXT, shadowLastChange INTEGER,
				shadowMin INTEGER, shadowMax INTEGER, shadowWarning INTEGER, shadowInactive INTEGER, shadowExpire INTEGER, shadowFlag INTEGER)',

			// Joomla's defaults: Manager (6) and its child Administrator (7) may log in to the administrator
			"INSERT INTO jos_usergroups VALUES (1, 0, 1, 12, 'Public'), (6, 1, 2, 5, 'Manager'), (7, 6, 3, 4, 'Administrator'),
				(2, 1, 6, 9, 'Registered'), (3, 2, 7, 8, 'Author'), (8, 1, 10, 11, 'Super Users')",
			'INSERT INTO jos_assets VALUES (1, 0, 0, 1, 0, \'root.1\', \'Root Asset\', \'{"core.login.site":{"6":1,"2":1},"core.login.admin":{"6":1},"core.login.offline":{"6":1},"core.admin":{"8":1},"core.manage":{"7":1}}\')',

			"INSERT INTO jos_users (id, name, username, email, secret, registerDate) VALUES
				(100, 'An Administrator', 'admin1', 'admin1@example.edu', 's1', '2020-01-01 00:00:00'),
				(101, 'A Member', 'member1', 'member1@example.edu', 's2', '2020-01-01 00:00:00')",
			// An unexpired local password, as afterOtherSignIn() reads it
			"INSERT INTO jos_users_password (user_id, passhash) VALUES (100, 'x'), (101, 'x')",
			'INSERT INTO jos_user_usergroup_map VALUES (100, 7), (100, 2), (101, 2)',
			"INSERT INTO jos_auth_domain VALUES (1, 'authentication', 'shibboleth', '" . ShibTestFixture::IDP . "', '')",
			"INSERT INTO jos_auth_link (id, auth_domain_id, username, email, user_id) VALUES
				(1, 1, 'admin1@example.edu', 'admin1@example.edu', 100),
				(2, 1, 'member1@example.edu', 'member1@example.edu', 101)",
		);

		foreach ($statements as $sql)
		{
			$db->setQuery($sql);
			if (!$db->query())
			{
				throw new RuntimeException('Fixture failed: ' . $sql);
			}
		}

		return $db;
	}
}
