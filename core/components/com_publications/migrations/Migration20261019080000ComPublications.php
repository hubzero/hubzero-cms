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
 * Migration for installing the table '#__publication_review' for anonymous review feature in publication component
 **/
class Migration20261019080000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_review'))
		{
			return;
		}

		$query = "CREATE TABLE `#__publication_review` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `publication_id` int(11) unsigned NOT NULL,
		  `version_number` int(11) unsigned NOT NULL DEFAULT '1',
		  `publication_version_id` int(11) unsigned NOT NULL,
		  `created` DATETIME DEFAULT CURRENT_TIMESTAMP,
		  `created_by` int(11) unsigned NOT NULL,
		  `expired` DATETIME DEFAULT NULL,
		  `token_hash` char(64) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `idx_publication_id_version_number` (`publication_id`, `version_number`),
		  KEY `idx_publication_version_id` (`publication_version_id`),
		  KEY `idx_created` (`created`),
		  KEY `idx_expired` (`expired`),
		  UNIQUE KEY `idx_token_hash` (`token_hash`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_review'))
		{
			$this->db->setQuery("DROP TABLE IF EXISTS `#__publication_review`;");
			$this->db->query();
		}
	}
}