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
 * Migration script for changing DATETIME fields default to NULL for xmessage tables
 **/
class Migration20190321000000Core extends Base
{
	/**
	 * List of tables and their datetime fields
	 *
	 * @var  array
	 **/
	public static $tables = array(
		'#__extensions' => array(
			'checked_out_time'
		),
		'#__menu' => array(
			'checked_out_time'
		)
	);

	/**
	 * Up
	 **/
	public function up()
	{
		foreach (self::$tables as $table => $fields)
		{
			foreach ($fields as $field)
			{
				if ($this->db->tableExists($table)
				 && $this->db->tableHasField($table, $field))
				{
					$query = "ALTER TABLE `$table` CHANGE `$field` `$field` DATETIME  NULL  DEFAULT NULL";

					$this->db->setQuery($query);
					$this->db->query();

					$this->log(sprintf('Changed `%s`.`%s` datetime default to NULL', $table, $field));

					$query = "UPDATE `$table` SET `$field`=NULL WHERE `$field`='0000-00-00 00:00:00'";

					$this->db->setQuery($query);
					$this->db->query();

					$this->log(sprintf('Cleaned up `%s`.`%s` datetime default values', $table, $field));
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		foreach (self::$tables as $table => $fields)
		{
			foreach ($fields as $field)
			{
				if ($this->db->tableExists($table)
				 && $this->db->tableHasField($table, $field))
				{
					$query = "ALTER TABLE `$table` CHANGE `$field` `$field` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00'";

					$this->db->setQuery($query);
					$this->db->query();

					$this->log(sprintf('Changed `%s`.`%s` datetime default to "0000-00-00 00:00:00"', $table, $field));
				}
			}
		}
	}
}
