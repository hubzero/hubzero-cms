<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding User - Secret plugin
 *
 * This migration script creates a jos_users column for the plugin to populate
 * with a unique user secret, on user login. On 'down' migration the column
 * is dropped from the table.
 *
 **/
class Migration20230909000000PlgUserSecret extends Base
{
	// specify table name
	static $tableName = '#__users';

	// specify column to create
	static $columnData = 	[ 
		['name' => 'secret',
		'type' => 'char(32)',
		'restriction' => 'UNIQUE',
		'default' => 'NULL'],
	];

	/**
	 * Up
	 *
	 * Create 'secret' column in jos_users if needed.
	 *
	 * Note that we have logging callbacks, per
	 * https://help.hubzero.org/documentation/22/webdevs/database/migrations
	 *
	 * This so-called logging provides informational output when 'muse' is run
	 * at up-migration time.
	 *
	 **/
	public function up()
	{
		$tableName = self::$tableName;
		$columnData = self::$columnData;

		// Add column to jos_users table to hold user secret
		// If the table exists, add column if it is absent:
		if ($this->db->tableExists($tableName))
		{
			if (!$this->db->tableHasField($tableName, $columnData['name'])) 
			{
				$this->log('No column `secret` found in table `jos_users`, creating...', 'info');

				// generate and run query to create column
				$query = $this->_generateSafeAddColumns($tableName, $columnData);
				$this->_queryIfTableExists($tableName, $query);

				$this->log('Column `secret` created in table `jos_users`', 'info');
			}
		}

		// now add the plugin to the db:
		$this->addPluginEntry('user', 'secret');
	}

	/**
	 * Down
	 *
	 * Drop 'secret' column from jos_users table.
	 *
	 * The so-called logging here provides informational output when 'muse' is run
	 * at down-migration time.
	 *
	 **/
	public function down()
	{
		$tableName = self::$tableName;
		$columnData = self::$columnData;

		// generate query to drop column from table:
		$query = $this->_generateSafeDropColumns($tableName, $columnData);

		// execute the query, if column is found in the specified table:
		$this->_queryIfTableExists($tableName, $query);

		$this->log('Column `secret` dropped from table `jos_users`', 'info');

		// now remove the plugin from the db:
		$this->deletePluginEntry('user', 'secret');
	}
}
