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
 * Migration script for installing templates table
 **/
class Migration20170901000000ComTemplates extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__template_styles'))
		{
			$query = "CREATE TABLE `#__template_styles` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `template` varchar(50) NOT NULL DEFAULT '',
			  `client_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
			  `home` char(7) NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `params` text NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `idx_template` (`template`),
			  KEY `idx_home` (`home`)
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
		if ($this->db->tableExists('#__template_styles'))
		{
			$query = "DROP TABLE IF EXISTS `#__template_styles`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
