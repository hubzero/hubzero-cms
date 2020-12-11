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
 * Migration script for adding component entry for com_installer
 **/
class Migration20201209000000ComInstaller extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__extension_types'))
		{
				$query = "CREATE TABLE `#__extension_types` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`type` varchar(150) NOT NULL DEFAULT '',
					PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

				$this->db->setQuery($query);
				$this->db->query();

				$query = "INSERT INTO `jos_extension_types` (`id`, `type`)
				VALUES
					(1,'component'),
					(2,'language'),
					(3,'library'),
					(4,'module'),
					(5,'plugin'),
					(6,'template'),
					(7,'non-standard');";

				$this->db->setQuery($query);
				$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__extension_types'))
		{
			$query = "DROP TABLE IF EXISTS `#__extension_types`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}







