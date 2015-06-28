<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for billboards table renames to match convention
 **/
class Migration20150306110808ComBillboards extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__billboard_collection') && !$this->db->tableExists('#__billboards_collections'))
		{
			$query = "RENAME TABLE `#__billboard_collection` TO `#__billboards_collections`";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableExists('#__billboards') && !$this->db->tableExists('#__billboards_billboards'))
		{
			$query = "RENAME TABLE `#__billboards` TO `#__billboards_billboards`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__billboard_collection') && $this->db->tableExists('#__billboards_collections'))
		{
			$query = "RENAME TABLE `#__billboards_collections` TO `#__billboard_collection`";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableExists('#__billboards') && $this->db->tableExists('#__billboards_billboards'))
		{
			$query = "RENAME TABLE `#__billboards_billboards` TO `#__billboards`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}