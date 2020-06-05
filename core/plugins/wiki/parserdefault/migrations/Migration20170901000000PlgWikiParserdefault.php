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
 * Migration script for installing wiki_formulas table
 **/
class Migration20170901000000PlgWikiParserdefault extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__wiki_formulas'))
		{
			$query = "CREATE TABLE `#__wiki_formulas` (
			  `inputhash` varchar(32) NOT NULL DEFAULT '',
			  `outputhash` varchar(32) NOT NULL DEFAULT '',
			  `conservativeness` tinyint(4) NOT NULL,
			  `html` text,
			  `mathml` text,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `uidx_inputhash` (`inputhash`)
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
		if ($this->db->tableExists('#__wiki_formulas'))
		{
			$query = "DROP TABLE IF EXISTS `#__wiki_formulas`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
