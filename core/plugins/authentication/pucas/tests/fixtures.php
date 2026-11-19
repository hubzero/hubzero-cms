<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Stand-ins for PucasLinkTest. Each covers only what the link lookups call.
 */

require_once PATH_CORE . '/plugins/authentication/pucas/pucas.php';

/**
 * The application, with the database below and the stand-ins
 */
class PucasTestFixture
{
	/**
	 * @param   string  $client  'administrator' or 'site'
	 * @return  \Hubzero\Base\Application
	 */
	public static function boot($client = 'administrator')
	{
		$app = new \Hubzero\Base\Application($client);
		$app['client']     = ($client == 'site') ? new \Hubzero\Base\Client\Site() : new \Hubzero\Base\Client\Administrator();
		$app['config']     = new \Hubzero\Config\Registry(array('secret' => 'test', 'cachetime' => 0));
		$app['dispatcher'] = new \Hubzero\Events\Dispatcher();
		$app['language']   = new \Hubzero\Test\Stubs\TranslatorStub();
		$app['component']  = new PucasTestComponents(array(
			'com_members' => array('allowUserRegistration' => 1)
		));
		$app['db'] = PucasTestSchema::create();

		\Hubzero\Facades\Facade::setApplication($app);

		return $app;
	}

	/**
	 * The plugin as the Guard constructs it
	 *
	 * @param   \Hubzero\Base\Application  $app
	 * @return  PucasTestPlugin
	 */
	public static function plugin($app)
	{
		return new PucasTestPlugin($app['dispatcher'], array(
			'type'   => 'authentication',
			'name'   => 'pucas',
			'params' => '{"admin_login":1,"site_login":1}'
		));
	}
}

/**
 * Opens the link lookups to the tests; they would otherwise need phpCAS
 */
class PucasTestPlugin extends plgAuthenticationPUCAS
{
	public function findLink($username, $options)
	{
		return parent::findLink($username, $options);
	}

	public function claimLink($username, $userId, $email)
	{
		return parent::claimLink($username, $userId, $email);
	}

	public static function matchingAccount($username)
	{
		return parent::matchingAccount($username);
	}
}

class PucasTestComponents
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
 * An in-memory SQLite database with two hub accounts and PUCAS links in
 * every state: owned, owned by someone else, and owned by nobody
 */
class PucasTestSchema
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
			'CREATE TABLE jos_auth_domain (id INTEGER PRIMARY KEY, type TEXT, authenticator TEXT, domain TEXT, params TEXT)',
			// user_id defaults to NULL, as in the MySQL schema
			'CREATE TABLE jos_auth_link (id INTEGER PRIMARY KEY, auth_domain_id INTEGER, username TEXT, email TEXT,
				password TEXT, params TEXT, user_id INTEGER DEFAULT NULL, linked_on TEXT)',

			// 'match1' has the username and purdue.edu address of its career account;
			// 'squat1' too, but never confirmed the address (activation < 0)
			"INSERT INTO jos_users (id, name, username, email, secret, registerDate, activation) VALUES
				(100, 'An Administrator', 'admin1', 'admin1@example.org', 's1', '2020-01-01 00:00:00', 1),
				(101, 'A Member', 'member1', 'member1@example.org', 's2', '2020-01-01 00:00:00', 1),
				(102, 'A Match', 'match1', 'match1@purdue.edu', 's3', '2020-01-01 00:00:00', 1),
				(103, 'A Squatter', 'squat1', 'squat1@purdue.edu', 's4', '2020-01-01 00:00:00', -12345)",
			// Domain '' as on the hubs, where link() used to look it up that way
			"INSERT INTO jos_auth_domain VALUES (1, 'authentication', 'pucas', '', '')",
			"INSERT INTO jos_auth_link (id, auth_domain_id, username, email, user_id) VALUES
				(1, 1, 'owned', 'owned@purdue.edu', 100),
				(2, 1, 'orphan', 'orphan@purdue.edu', NULL),
				(3, 1, 'orphan0', 'orphan0@purdue.edu', 0),
				(4, 1, 'members', 'members@purdue.edu', 101)",
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
