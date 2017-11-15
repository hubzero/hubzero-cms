<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding 'derivatives' field to publication licenses
 **/
class Migration20170712200400ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_licenses'))
		{
			if (!$this->db->tableHasField('#__publication_licenses', 'derivatives'))
			{
				$query = "ALTER TABLE `#__publication_licenses` ADD COLUMN `derivatives` tinyint(2) NOT NULL default 0;";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__publication_licenses` SET `derivatives`=1 WHERE `name` IN ('cc', 'standard', 'cc0', 'cc40-by-nc-sa', 'cc40-by-sa')";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_licenses'))
		{
			if ($this->db->tableHasField('#__publication_licenses', 'derivatives'))
			{
				$query = "ALTER TABLE `#__publication_licenses` DROP `derivatives`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
