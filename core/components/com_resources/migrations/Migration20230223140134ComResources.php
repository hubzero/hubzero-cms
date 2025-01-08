<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing DATETIME fields default to NULL for com_resources
 **/
class Migration20230223140134ComResources extends Base
{
	/**
	 * List of tables and their datetime fields
	 *
	 * @var  array
	 **/
	public static $tables = array(
		'#__author_roles' => array(
			'created',
			'modified'
		)
	);

	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__resource_acl_group'))
		{
			$query = "CREATE TABLE `#__resource_acl_group` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`resource_id` int(11) NOT NULL,
				`group_id` int(11) NOT NULL,
				PRIMARY KEY (`id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}

		// Taxonomy
		if (!$this->db->tableExists('#__resource_acl_user'))
		{
			$query = "CREATE TABLE `#__resource_acl_user` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`resource_id` int(11) NOT NULL,
				`user_id` int(11) NOT NULL,
				PRIMARY KEY (`id`)
			  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__resource_acl_group'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_acl_group`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__resource_acl_user'))
		{
			$query = "DROP TABLE IF EXISTS `#__resource_acl_user`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
