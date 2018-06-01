<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20171201191210ComTags extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__focus_area_publication_master_type_rel'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__focus_area_publication_master_type_rel` (
			    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	 	 		`focus_area_id` int(11) NOT NULL, 
	  			`master_type_id` int(11) NOT NULL,
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
		if ($this->db->tableExists('#__focus_area_publication_master_type_rel'))
		{
			$query = "DROP TABLE #__focus_area_publication_master_type_rel";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
