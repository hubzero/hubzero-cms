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
 * Migration script for renaming metrics_author_cluster if it exists, creating it otherwise
 **/
class Migration20130827143717Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('metrics_author_cluster') && !$this->db->tableExists('#__metrics_author_cluster'))
		{
			$query = "RENAME TABLE `metrics_author_cluster` TO `#__metrics_author_cluster`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		else if (!$this->db->tableExists('metrics_author_cluster') && !$this->db->tableExists('#__metrics_author_cluster'))
		{
			$query = "CREATE TABLE `#__metrics_author_cluster` (
						`authorid` varchar(60) NOT NULL DEFAULT '0',
						`classes` int(11) DEFAULT '0',
						`users` int(11) DEFAULT '0',
						`schools` int(11) DEFAULT '0',
						PRIMARY KEY (`authorid`)
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
		if (!$this->db->tableExists('metrics_author_cluster') && $this->db->tableExists('#__metrics_author_cluster'))
		{
			$query = "RENAME TABLE `#__metrics_author_cluster` TO `metrics_author_cluster`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
