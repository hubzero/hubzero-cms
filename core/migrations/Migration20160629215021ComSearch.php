<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating the indexQueue table
 **/
class Migration20160629215021ComSearch extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__search_indexqueue'))
		{
			$createQuery = 'CREATE TABLE `#__search_indexqueue` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`hubtype` varchar(12) NOT NULL DEFAULT \'\',
			`action` varchar(12) DEFAULT NULL,
			`start` int(11) NOT NULL DEFAULT \'0\',
			`lock` tinyint(1) NOT NULL DEFAULT \'0\',
			`complete` tinyint(1) NOT NULL DEFAULT \'0\',
			`created` timestamp NULL DEFAULT NULL,
			`created_by` int(11) DEFAULT NULL,
			`modified` timestamp NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;';

			$this->db->setQuery($createQuery);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__search_indexqueue'))
		{
			$dropQuery = 'DROP TABLE `#__search_indexqueue`';
			$this->db->setQuery($dropQuery);
			$this->db->query();
		}
	}
}
