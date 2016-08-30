<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding citations block and fixing block order in #_publication_blocks
 **/
class Migration20151004090000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_blocks'))
		{
			$queries = array();

			$queries[] = "INSERT INTO `#__publication_blocks` (`block`, `label`, `title`, `status`,
						 `minimum`, `maximum`, `ordering`, `params`, `manifest`)
				SELECT 'citations','Citations','Publication Citations',1,'default=1',0,1,7,''
				FROM DUAL WHERE NOT EXISTS (SELECT `block` FROM `#__publication_blocks` WHERE `block` = 'citations');";

			$queries[] = "UPDATE `#__publication_blocks` SET ordering='8' WHERE block='notes';\n";
			$queries[] = "UPDATE `#__publication_blocks` SET ordering='9' WHERE block='review';\n";

			// Run queries
			foreach ($queries as $query)
			{
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}