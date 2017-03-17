<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating the facet table for Solr
 **/
class Migration20170220041701ComSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__solr_search_facets'))
		{
			$sql = "CREATE TABLE `#__solr_search_facets` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				`facet` longtext,
				`state` tinyint(4) NOT NULL DEFAULT '0',
				`protected` tinyint(4) NOT NULL DEFAULT '0',
				`ordering` varchar(45) NOT NULL DEFAULT '0',
				`parent_id` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($sql);
			$this->db->query();

			$insert = "
				INSERT INTO `jos_solr_search_facets` (`id`,`name`,`facet`,`state`,`protected`,`ordering`,`parent_id`) VALUES
				(1,'Content','hubtype:content',1,1,'0',0), (2,'Resources','hubtype:resource',1,1,'0',0),
				(3,'Collections','hubtype:collection',1,1,'0',0), (4,'Members','hubtype:member',1,1,'0',0),
				(5,'Projects','hubtype:project',1,1,'0',0), (6,'Groups','hubtype:group',1,1,'0',0), 
				(7,'Courses','hubtype:course',1,1,'0',0), (8,'Wiki','hubtype:wiki',1,1,'0',0),
				(9,'Events','hubtype:event',1,1,'0',0), (10,'Knowledge Base Article','hubtype:kb-article',1,1,'0',0),
				(11,'Blog Posts','hubtype:blog-entry',1,1,'0',0), (12,'Wishes','hubtype:wishlist',1,1,'0',0),
				(13,'Publications','hubtype:publication',1,1,'0',0), (14,'Questions','hubtype:question',1,1,'0',0),
				(15,'Citations','hubtype:citation',1,1,'0',0);";

			$this->db->setQuery($insert);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__solr_search_facets'))
		{
			$sql = "DROP TABLE #__solr_search_facets;";
			$this->db->setQuery($sql);
			$this->db->query();
		}
	}
}
