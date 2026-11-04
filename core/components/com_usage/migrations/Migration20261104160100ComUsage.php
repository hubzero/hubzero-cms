<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Exception\QueryFailedException;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to make the usage tables' IP columns wide enough for IPv6
 *
 * The metrics database's usage tables store the client address in
 * varchar(15), which holds only dotted IPv4. They become varchar(45), as the
 * CMS tables did in Migration20261104160000CoreIpv6.
 *
 * userlogin.ip is also stored as ascii: its unique key spans five columns,
 * and at varchar(45) utf8 that key would be over MyISAM's 1000 bytes. MySQL
 * refuses that, and MariaDB 10.4+ quietly turns the key into a hash. An IP
 * address is ASCII text, and as ascii the key stays the size it was.
 *
 * The tables are MyISAM and can be large; ALTER copies each, holding a
 * table lock while it does, so on a busy hub this can take a while. That
 * happens in the CMS database when the metrics tables are kept there (no
 * separate stats database configured).
 *
 * The failure of any ALTER here marks the migration skipped rather than
 * stopping the run, so it is tried again on the next one.
 **/
class Migration20261104160100ComUsage extends Base
{
	/**
	 * IP address columns, by table, with the character set to store each in
	 * (null to keep its own)
	 *
	 * @var  array
	 **/
	protected $columns = array(
		'sessionlog_metrics' => array('ip', null),
		'toolstart'          => array('ip', null),
		'userlogin'          => array('ip', 'ascii'),
		'web'                => array('ip', null),
		'websessions'        => array('ip', null)
	);

	/**
	 * Up
	 **/
	public function up()
	{
		$statsDb = $this->statsDb();

		if (!$statsDb)
		{
			return;
		}

		// The runner relaxes sql_mode on the CMS connection only. These tables
		// have zero-date defaults, which an ALTER under NO_ZERO_DATE refuses.
		$sqlMode = null;

		try
		{
			$statsDb->setQuery('SELECT @@SESSION.sql_mode');
			$sqlMode = $statsDb->loadResult();

			$statsDb->setQuery("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
			$statsDb->query();

			$found = false;

			foreach ($this->columns as $table => $column)
			{
				if ($statsDb->tableExists($table))
				{
					$found = true;
					$this->widenColumn($table, $column[0], 45, $column[1], $statsDb);
				}
			}

			if (!$found)
			{
				$this->log('No usage tables in the metrics database; nothing to widen.');
			}
		}
		catch (QueryFailedException $e)
		{
			$this->setError('Could not widen the usage IP columns, will try again on the next run: ' . $e->getMessage(), 'skipped');
		}
		finally
		{
			if ($sqlMode !== null)
			{
				$statsDb->setQuery('SET SESSION sql_mode = ' . $statsDb->quote($sqlMode));
				$statsDb->query();
			}
		}
	}

	/**
	 * Down
	 *
	 * Narrowing the columns again would cut short every IPv6 address stored
	 * since, so leave them as they are
	 **/
	public function down()
	{
	}

	/**
	 * The metrics database connection, or null after marking the migration
	 * skipped
	 *
	 * com_usage's helper gives the CMS connection both when no separate
	 * stats database is configured and when the configured one cannot be
	 * reached. Only the first is the metrics database.
	 *
	 * @return  object|null
	 **/
	protected function statsDb()
	{
		if (!file_exists(dirname(__DIR__) . '/helpers/helper.php'))
		{
			$this->log('Unable to locate usage helper class.', 'error');
			return null;
		}

		include_once dirname(__DIR__) . '/helpers/helper.php';

		$statsDb = \Components\Usage\Helpers\Helper::getUDBO();

		$config     = \Component::params('com_usage');
		$configured = $config->get('statsDBDatabase') != ''
			|| $config->get('statsDBUsername') != ''
			|| $config->get('statsDBPassword') != '';

		if (!$statsDb || ($configured && $statsDb === \App::get('db')))
		{
			$this->setError('Unable to connect to the metrics database, will try again on the next run.', 'skipped');
			return null;
		}

		return $statsDb;
	}
}
