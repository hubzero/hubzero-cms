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
 * Migration script for setting up databases projects plugin
 **/
class Migration20130813210535PlgProjectsDatabases extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__project_databases'))
		{
			$query = "CREATE TABLE `#__project_databases` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`project` int(11) NOT NULL,
						`database_name` varchar(64) NOT NULL,
						`title` varchar(127) NOT NULL DEFAULT '',
						`source_file` varchar(127) NOT NULL,
						`source_dir` varchar(127) NOT NULL,
						`source_revision` varchar(56) NOT NULL,
						`description` text,
						`data_definition` text,
						`revision` int(11) DEFAULT NULL,
						`created` datetime DEFAULT NULL,
						`created_by` int(11) DEFAULT NULL,
						`updated` datetime DEFAULT NULL,
						`updated_by` int(11) DEFAULT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__project_database_versions'))
		{
			$query = "CREATE TABLE `#__project_database_versions` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`database_name` varchar(64) NOT NULL,
						`version` int(11) NOT NULL DEFAULT '1',
						`data_definition` text,
						PRIMARY KEY (`id`,`database_name`,`version`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->addPluginEntry('projects', 'databases', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__project_databases'))
		{
			$query = "DROP TABLE `#__project_databases`";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__project_database_versions'))
		{
			$query = "DROP TABLE `#__project_database_versions`;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->deletePluginEntry('projects', 'databases');
	}
}
