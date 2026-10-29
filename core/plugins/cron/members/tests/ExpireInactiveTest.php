<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Members\Tests;

use Hubzero\Test\Basic;
use PDO;
use plgCronMembers;

require_once dirname(__DIR__) . DS . 'members.php';

/**
 * Selection of inactive accounts by the expireInactiveAccounts cron event
 *
 * The query test needs a scratch MySQL/MariaDB database and is skipped
 * without one. It builds #__users and #__users_password from the install
 * schema, so the columns are the real ones. Run it with, for example:
 *
 *   docker run -d --name hz-test-db -e MARIADB_ROOT_PASSWORD=t -e MARIADB_DATABASE=t mariadb:10.11
 *   HZ_TEST_DB_DSN='mysql:host=<ip>;dbname=t' HZ_TEST_DB_USER=root HZ_TEST_DB_PASS=t \
 *     vendor/bin/phpunit --bootstrap bootstrap/phpunit-bootstrap.php plugins/cron/members/tests
 */
class ExpireInactiveTest extends Basic
{
	/**
	 * Only whole numbers of months, at least 1, are accepted
	 *
	 * @return  void
	 */
	public function testInactivityMonths()
	{
		$this->assertSame(36, plgCronMembers::inactivityMonths('36'));
		$this->assertSame(36, plgCronMembers::inactivityMonths(36));
		$this->assertSame(12, plgCronMembers::inactivityMonths(' 12 '));
		$this->assertSame(1, plgCronMembers::inactivityMonths('1'));

		foreach (array('0', 0, '-1', '1.5', 'abc', '', null) as $bad)
		{
			$this->assertSame(0, plgCronMembers::inactivityMonths($bad), var_export($bad, true) . ' must be refused');
		}
	}

	/**
	 * Exactly the accounts with no activity since the cutoff, and no
	 * account expiry already set, are selected
	 *
	 * @return  void
	 */
	public function testInactiveAccountsQuery()
	{
		$dsn = getenv('HZ_TEST_DB_DSN');

		if (!$dsn)
		{
			$this->markTestSkipped('HZ_TEST_DB_DSN not set: no scratch database');
		}

		$db = new PDO($dsn, getenv('HZ_TEST_DB_USER'), getenv('HZ_TEST_DB_PASS'));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// The install schema's '0000-00-00' defaults need a permissive mode
		$db->exec("SET SESSION sql_mode = ''");

		foreach (array('users', 'users_password', 'usergroups', 'user_usergroup_map') as $table)
		{
			$db->exec('DROP TABLE IF EXISTS `jos_' . $table . '`');
			$db->exec(str_replace('#__', 'jos_', self::schemaTable($table)));
		}

		$cutoff    = '2026-10-01 00:00:00';
		$cutoffDay = intval(strtotime($cutoff . ' UTC') / 86400);
		$zero      = '0000-00-00 00:00:00';

		// id => [block, registerDate, lastvisitDate, password row: null or [shadowLastChange, shadowExpire]]
		$users = array(
			1  => array(0, '2015-01-01 00:00:00', '2026-10-15 00:00:00', array(16000, null)),        // visit after the cutoff
			2  => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', array(16000, null)),        // old visit
			3  => array(0, '2019-01-01 00:00:00', $zero, array(null, null)),                         // never visited, old registration
			4  => array(0, '2026-10-15 00:00:00', $zero, array(null, null)),                         // never visited, registered after the cutoff
			5  => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', array($cutoffDay + 10, null)), // old visit, recent password change
			6  => array(1, '2015-01-01 00:00:00', '2020-01-01 00:00:00', array(16000, null)),        // blocked
			7  => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', array(16000, 1)),           // not yet reset
			8  => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', array(16000, 333)),         // already inactive
			9  => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', array(16000, 10957)),       // blocked marker
			10 => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', null),                      // no password row
			11 => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', array($cutoffDay, null)),   // password changed on the cutoff day
			12 => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', array(16000, null)),        // in the exempt group
			13 => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', null),                      // in the exempt group, no password row
			14 => array(0, '2015-01-01 00:00:00', '2020-01-01 00:00:00', array(16000, null)),        // in another group only
		);

		$insertUser = $db->prepare("INSERT INTO jos_users (id, username, block, registerDate, lastvisitDate, params) VALUES (?, ?, ?, ?, ?, '')");
		$insertPass = $db->prepare("INSERT INTO jos_users_password (user_id, passhash, shadowLastChange, shadowExpire) VALUES (?, '', ?, ?)");

		foreach ($users as $id => $u)
		{
			$insertUser->execute(array($id, 'user' . $id, $u[0], $u[1], $u[2]));

			if ($u[3] !== null)
			{
				$insertPass->execute(array($id, $u[3][0], $u[3][1]));
			}
		}

		$db->exec("INSERT INTO jos_usergroups (id, parent_id, title) VALUES (2, 1, 'Registered'), (9, 2, 'Service accounts')");
		$db->exec("INSERT INTO jos_user_usergroup_map (user_id, group_id) VALUES (12, 9), (13, 9), (14, 2), (2, 2)");

		$select = function ($exemptGroup) use ($db, $cutoff, $cutoffDay)
		{
			$query = plgCronMembers::inactiveAccountsQuery($db->quote($cutoff), $cutoffDay, $exemptGroup === null ? null : $db->quote($exemptGroup));
			$ids   = array_map('intval', $db->query(str_replace('#__', 'jos_', $query))->fetchAll(PDO::FETCH_COLUMN));
			sort($ids);
			return $ids;
		};

		$this->assertSame(array(2, 3, 10, 14), $select('Service accounts'));
		$this->assertSame(array(2, 3, 10, 12, 13, 14), $select(null), 'no exempt group: group members are selected too');
		$this->assertSame(array(2, 3, 10, 12, 13, 14), $select('No such group'));
	}

	/**
	 * A table's CREATE statement from the install schema
	 *
	 * @param   string  $table  Name without prefix
	 * @return  string
	 */
	private static function schemaTable($table)
	{
		$schema = file_get_contents(PATH_CORE . '/bootstrap/Install/sql/mysql/schema.sql');

		if (!preg_match('/CREATE TABLE `#__' . preg_quote($table, '/') . '` \(.*?\) ENGINE=[^;]*;/s', $schema, $m))
		{
			throw new \RuntimeException('#__' . $table . ' not found in schema.sql');
		}

		return $m[0];
	}
}
