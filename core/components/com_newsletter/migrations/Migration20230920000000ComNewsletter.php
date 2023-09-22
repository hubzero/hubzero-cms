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
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('campaign'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `campaign` (
    			id INT(11) NOT NULL AUTO_INCREMENT,
    			title VARCHAR(50),
    			description TEXT,
    			campaign_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				secret CHAR(32) UNIQUE NULL,
				modified  DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
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
		if ($this->db->tableExists('campaign'))
		{
			$query = "DROP TABLE IF EXISTS `campaign`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
