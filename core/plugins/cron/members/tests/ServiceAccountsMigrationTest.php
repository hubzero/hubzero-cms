<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Members\Tests;

use Hubzero\Database\Driver;
use Hubzero\Test\Basic;
use Migration20261029000001PlgCronMembersServiceAccounts;
use PDO;

require_once dirname(__DIR__) . DS . 'migrations' . DS . 'Migration20261029000001PlgCronMembersServiceAccounts.php';

/**
 * The migration that creates the "Service accounts" access group
 *
 * Needs a scratch MySQL/MariaDB database and is skipped without one (see
 * ExpireInactiveTest). The tables and the default access groups come from the
 * install schema and data.
 */
class ServiceAccountsMigrationTest extends Basic
{
	/**
	 * Group created once under the root, nested set kept consistent, existing
	 * service accounts added, and down() restores the tree
	 *
	 * @return  void
	 */
	public function testUpAndDown()
	{
		$dsn = getenv('HZ_TEST_DB_DSN');

		if (!$dsn)
		{
			$this->markTestSkipped('HZ_TEST_DB_DSN not set: no scratch database');
		}

		$pdo = new PDO($dsn, getenv('HZ_TEST_DB_USER'), getenv('HZ_TEST_DB_PASS'));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->exec("SET SESSION sql_mode = ''");

		$sql = PATH_CORE . '/bootstrap/Install/sql/mysql/';

		foreach (array('users', 'usergroups', 'user_usergroup_map') as $table)
		{
			$pdo->exec('DROP TABLE IF EXISTS `jos_' . $table . '`');
			$pdo->exec(str_replace('#__', 'jos_', self::statement($sql . 'schema.sql', '/CREATE TABLE `#__' . $table . '` \(.*?\) ENGINE=[^;]*;/s')));
		}

		preg_match_all('/INSERT INTO `#__usergroups` .*?;/s', file_get_contents($sql . 'data.sql'), $inserts);
		$this->assertNotEmpty($inserts[0]);
		foreach ($inserts[0] as $insert)
		{
			$pdo->exec(str_replace('#__', 'jos_', $insert));
		}

		// hubzerosolrworker is absent on this hub
		$pdo->exec("INSERT INTO jos_users (id, username, params) VALUES (1001, 'apps', ''), (1002, 'gridstat', ''), (1003, 'someone', '')");

		$before = self::tree($pdo);

		$db = Driver::getInstance(array('driver' => 'pdo', 'dsn' => $dsn, 'user' => getenv('HZ_TEST_DB_USER'), 'password' => getenv('HZ_TEST_DB_PASS'), 'prefix' => 'jos_'));
		$migration = new Migration20261029000001PlgCronMembersServiceAccounts($db);

		$migration->up();
		$migration->up();

		$after = self::tree($pdo);
		$group = array_values(array_filter($after, function ($row)
		{
			return $row['title'] == 'Service accounts';
		}));

		$this->assertCount(1, $group, 'created exactly once');
		$this->assertSame(0, (int) $before[1]['parent_id']);
		$this->assertSame(1, (int) $group[0]['parent_id'], 'directly under Public');
		$this->assertTrue(self::nestedSetIsValid($after), 'nested set consistent after up()');

		$members = $pdo->query('SELECT user_id FROM jos_user_usergroup_map WHERE group_id = ' . (int) $group[0]['id'] . ' ORDER BY user_id')->fetchAll(PDO::FETCH_COLUMN);
		$this->assertSame(array(1001, 1002), array_map('intval', $members));

		// A group an admin creates later, to the right of "Service accounts"
		$rgt = (int) $pdo->query('SELECT rgt FROM jos_usergroups WHERE parent_id = 0')->fetchColumn();
		$pdo->exec('UPDATE jos_usergroups SET rgt = rgt + 2 WHERE rgt >= ' . $rgt);
		$pdo->exec("INSERT INTO jos_usergroups (id, parent_id, lft, rgt, title) VALUES (50, 1, $rgt, " . ($rgt + 1) . ", 'Later')");
		$this->assertTrue(self::nestedSetIsValid(self::tree($pdo)), 'fixture: nested set consistent with the later group');

		$migration->down();

		$expected = $before;
		$expected[1]['rgt'] = (int) $before[1]['rgt'] + 2;
		$expected[50] = array('id' => 50, 'parent_id' => 1, 'lft' => (int) $before[1]['rgt'], 'rgt' => (int) $before[1]['rgt'] + 1, 'title' => 'Later');
		$this->assertEquals($expected, self::tree($pdo), 'down() removes the group and closes its gap');
		$this->assertTrue(self::nestedSetIsValid(self::tree($pdo)), 'nested set consistent after down()');
		$this->assertSame(0, (int) $pdo->query('SELECT COUNT(*) FROM jos_user_usergroup_map')->fetchColumn());
	}

	/**
	 * Access groups by id
	 *
	 * @param   object  $pdo
	 * @return  array
	 */
	private static function tree($pdo)
	{
		$rows = array();
		foreach ($pdo->query('SELECT id, parent_id, lft, rgt, title FROM jos_usergroups ORDER BY id')->fetchAll(PDO::FETCH_ASSOC) as $row)
		{
			$rows[$row['id']] = $row;
		}
		return $rows;
	}

	/**
	 * Every node's interval lies strictly inside its parent's, any two
	 * intervals are disjoint or nested, and no bound is used twice. Gaps are
	 * allowed: the install data has them.
	 *
	 * @param   array  $rows
	 * @return  bool
	 */
	private static function nestedSetIsValid($rows)
	{
		$bounds = array();

		foreach ($rows as $row)
		{
			$bounds[] = (int) $row['lft'];
			$bounds[] = (int) $row['rgt'];

			if ((int) $row['lft'] >= (int) $row['rgt'])
			{
				return false;
			}

			if ($row['parent_id'] != 0)
			{
				$parent = $rows[$row['parent_id']];
				if (!((int) $parent['lft'] < (int) $row['lft'] && (int) $row['rgt'] < (int) $parent['rgt']))
				{
					return false;
				}
			}

			foreach ($rows as $other)
			{
				$a = array((int) $row['lft'], (int) $row['rgt']);
				$b = array((int) $other['lft'], (int) $other['rgt']);
				$disjoint = ($a[1] < $b[0] || $b[1] < $a[0]);
				$nested   = ($a[0] <= $b[0] && $b[1] <= $a[1]) || ($b[0] <= $a[0] && $a[1] <= $b[1]);
				if (!$disjoint && !$nested)
				{
					return false;
				}
			}
		}

		return count($bounds) === count(array_unique($bounds));
	}

	/**
	 * First statement in an SQL file matching a pattern
	 *
	 * @param   string  $file
	 * @param   string  $pattern
	 * @return  string
	 */
	private static function statement($file, $pattern)
	{
		if (!preg_match($pattern, file_get_contents($file), $m))
		{
			throw new \RuntimeException($pattern . ' not found in ' . $file);
		}

		return $m[0];
	}
}
