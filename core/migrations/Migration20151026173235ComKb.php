<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for replacing HUBADDRESS references in KB article
 **/
class Migration20151026173235ComKb extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__faq'))
		{
			$query = "UPDATE `#__faq` SET `fulltxt` = REPLACE(`fulltxt`, 'HUBADDRESS', '{xhub:getcfg hubHostname}')";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__kb_articles'))
		{
			$query = "UPDATE `#__kb_articles` SET `fulltxt` = REPLACE(`fulltxt`, 'HUBADDRESS', '{xhub:getcfg hubHostname}')";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}