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
 * Migration script for adding User Secret to jos_users table
 *
 * This migration script creates a jos_users column to populate
 * with a unique user secret, on user login. On 'down' migration the column
 * is dropped from the table.
 *
 **/
class Migration20231016000000ComMembers extends Base
{
	// specify table name
	static $tableName = '#__users';

	// specify column to create
	static $columnData = 	[['name' => 'secret',
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

			}
			// Generate and save user secrets
			$this->generateUserSecrets();
		}
	}

	/**
	 * Down
	 *
	 * Drop 'secret' column from jos_users table.
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
	}

	/**
	 * generateUserSecrets()
	 *
	 * Populate 'secret' column in jos_users table for those users who have
	 * logged in over the last year, but have no secret set. Each secret is a
	 * unique, random 32-character string.
	 *
	 **/
	private function generateUserSecrets()
	{
		// User secret will have length 32 characters:
		$secretLength = 32;

		// Criteria: create secrets for users that have logged in during the last year
		$criteria = 'DATE_SUB(CURDATE(), INTERVAL 1 YEAR';

		// Fetch ids for all such users who have no secret
		$targetUsers = \Hubzero\User\User::all()
						->where('lastvisitdate', '>', $criteria)
						->whereIsNull('secret');

		// Create and save a unique secret for each such user:
		foreach ($targetUsers as $oneUser)
		{
			$newSecret = \Hubzero\User\Password::genRandomPassword($secretLength);
			$oneUser->set('secret', $newSecret);
			$oneUser->save();
		}
		$this->log('Saved new secrets for target users', 'info');
	}
}
