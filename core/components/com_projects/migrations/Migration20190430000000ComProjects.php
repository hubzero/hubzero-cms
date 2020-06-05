<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing DATETIME fields default to NULL for com_projects
 **/
class Migration20190430000000ComProjects extends Base
{
	/**
	 * List of tables and their datetime fields
	 *
	 * @var  array
	 **/
	public static $table = '#__projects';

	/**
	 * Get view level ID for a title
	 *
	 * @param   string   $title
	 * @return  integer
	 **/
	private function getViewLevel($title, $default = 0)
	{
		static $access;

		if (!$access)
		{
			$query = "SELECT * FROM `#__viewlevels`";
			$this->db->setQuery($query);
			$access = $this->db->loadObjectList();
		}

		$val = $default;

		foreach ($access as $level)
		{
			if (strtolower($level->title) == $title)
			{
				$val = $level->id;
				break;
			}
		}

		return $val;
	}

	/**
	 * Up
	 **/
	public function up()
	{
		$table = self::$table;

		if ($this->db->tableExists($table)
		 && $this->db->tableHasField($table, 'private')
		 && !$this->db->tableHasField($table, 'access'))
		{
			$query = "ALTER TABLE `$table` ADD COLUMN `access` INT(11)  NOT NULL  DEFAULT '0'";

			$this->db->setQuery($query);
			$this->db->query();

			$this->log(sprintf('Adding column `access` to table %s', $table));

			if (!$this->db->tableHasKey($table, 'idx_access'))
			{
				$query = "ALTER TABLE `$table` ADD INDEX `idx_access` (`access`)";
				$this->db->setQuery($query);
				$this->db->query();

				$this->log(sprintf('Adding index `idx_access` on column `access` to table %s', $table));
			}

			$query = "SELECT * FROM `#__viewlevels`";
			$this->db->setQuery($query);
			$access = $this->db->loadObjectList();

			// Private
			$val = $this->getViewLevel('private', 5);

			$query = "UPDATE `$table` SET `access`=$val WHERE `private`=1";

			$this->db->setQuery($query);
			$this->db->query();

			$this->log(sprintf('Setting `access` = %s (private) where `private` = 1 on table %s', $val, $table));

			// Public
			//$val = $this->getViewLevel('protected', 4);
			$val = $this->getViewLevel('public', 1);

			$query = "UPDATE `$table` SET `access`=$val WHERE `private`=0";

			$this->db->setQuery($query);
			$this->db->query();

			$this->log(sprintf('Setting `access` = %s (public) where `private` = 0 on table %s', $val, $table));

			// Open
			$val = $this->getViewLevel('public', 1);

			$query = "UPDATE `$table` SET `access`=$val WHERE `private`<0";

			$this->db->setQuery($query);
			$this->db->query();

			$this->log(sprintf('Setting `access` = %s (public) where `private` < 0 on table %s', $val, $table));
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$table = self::$table;

		if ($this->db->tableExists($table)
		 && $this->db->tableHasField($table, 'access'))
		{
			if ($this->db->tableHasKey($table, 'idx_access'))
			{
				$query = "ALTER TABLE `$table` DROP KEY `idx_access`";
				$this->db->setQuery($query);
				$this->db->query();

				$this->log(sprintf('Dropping index `idx_access` on column `access` from table %s', $table));
			}

			$query = "ALTER TABLE `$table` DROP COLUMN `access`;";

			$this->db->setQuery($query);
			$this->db->query();

			$this->log(sprintf('Dropping column `access` from table %s', $table));
		}
	}
}
