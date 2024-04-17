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
 * Migration script for managing campaigns and their secrets via com_newsletter
 **/
class Migration20230920000000ComNewsletter extends Base
{
	public static $table = '#__campaign';

	/**
	 * Up
	 **/
	public function up()
	{
		$table = self::$table;

		if (!$this->db->tableExists($table))
		{
			$query = "CREATE TABLE IF NOT EXISTS `$table` (
				id INT(11) NOT NULL AUTO_INCREMENT,
				title VARCHAR(50),
				`description` TEXT,
				campaign_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				`secret` CHAR(32) UNIQUE NULL,
				modified  DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				expire_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				modified_by int(11) DEFAULT NULL,
				PRIMARY KEY (id),
				KEY idx_title (title)
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
		$table = self::$table;

		if ($this->db->tableExists($table))
		{
			$query = "DROP TABLE IF EXISTS `$table`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
