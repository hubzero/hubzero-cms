<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing DATETIME fields default to NULL for com_projects
 **/
class Migration20190221000000ComProjects extends Base
{
	/**
	 * List of tables and their datetime fields
	 *
	 * @var  array
	 **/
	public static $tables = array(
		'#__project_activity' => array(
			'recorded'
		),
		'#__project_comments' => array(
			'created'
		),
		'#__project_logs' => array(
			'time'
		),
		'#__project_microblog' => array(
			'posted'
		),
		'#__project_owners' => array(
			'added'
		),
		'#__project_repos' => array(
			'created'
		),
		'#__project_stats' => array(
			'processed'
		),
		'#__project_todo' => array(
			'created'
		),
		'#__project_tool_instances' => array(
			'created'
		),
		'#__project_tool_logs' => array(
			'recorded'
		),
		'#__project_tool_views' => array(
			'viewed'
		),
		'#__project_tools' => array(
			'created'
		),
		'#__projects' => array(
			'created'
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

					$query = "UPDATE `$table` SET `$field`=NULL WHERE `$field`='0000-00-00 00:00:00'";

					$this->db->setQuery($query);
					$this->db->query();
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
				}
			}
		}
	}
}
