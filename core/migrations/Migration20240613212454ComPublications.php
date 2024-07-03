<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating publication collaborator table
 **/
class Migration20240613212454ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__publication_collaborators'))
		{
			$query = "CREATE TABLE `#__publication_collaborators` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(100) DEFAULT NULL,
			  `orcid` varchar(100) DEFAULT NULL,
			  `access_token` varchar(100) DEFAULT NULL,
			  `acquisition_date`datetime DEFAULT NULL,
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
		if ($this->db->tableExists('#__publication_collaborators'))
		{
			$query = "DROP TABLE IF EXISTS `#__publication_collaborators`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
