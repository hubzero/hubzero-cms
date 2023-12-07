<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding User Secret to plugin
 *
 * This migration script checks for jos_users column for the plugin to populate
 * with a unique user secret, on user login. Creation of this column is done
 * on up migration in com_members component.
 *
 **/
class Migration20230909000000PlgUserHubzero extends Base
{
	// specify table name
	static $tableName = '#__users';

	// specify column to create
	static $columnData = [['name' => 'secret',
		'type' => 'char(32)',
		'restriction' => 'UNIQUE',
		'default' => 'NULL'],
	];

	/**
	 * Up
	 *
	 * Check for 'secret' column in jos_users table.
	 *
	 * Note that we have logging callbacks, per
	 * https://help.hubzero.org/documentation/22/webdevs/database/migrations
	 *
	 **/
	public function up()
	{
		$tableName = self::$tableName;
		$columnData = self::$columnData;

		// Check for column in jos_users table to hold user secret
		if ($this->db->tableExists($tableName))
		{
			// If the table exists, and column is absent, err out:
			if (!$this->db->tableHasField($tableName, $columnData[0]['name'])) {
				$this->log('No column `secret` found in table `jos_users`, stopping...', 'error');
				$this->setError('Run migration for com_members component first');

			} else {
				$this->log('Found `secret` column in table `jos_users`, success...', 'success');
			}
		}
	}

	/**
	 * Down
	 *
	 * No change is made for down migration.
	 *
	 **/
	public function down()
	{
		// No-op
	}
}
