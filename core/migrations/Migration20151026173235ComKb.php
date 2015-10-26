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
		$query = "UPDATE `#__faq` SET `fulltxt` = REPLACE(`fulltxt`, 'HUBADDRESS', '{xhub:getcfg hubHostname}')";
		$this->db->setQuery($query);
		$this->db->query();
	}
}