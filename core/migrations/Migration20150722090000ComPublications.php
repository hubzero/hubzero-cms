<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing unused flag from publication_licenses
 **/
class Migration20150722090000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_licenses')
			&& $this->db->tableHasField('#__publication_licenses', 'apps_only')
		)
		{
			$query = "ALTER TABLE `#__publication_licenses` DROP COLUMN `apps_only`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}