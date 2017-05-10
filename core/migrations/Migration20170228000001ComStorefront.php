<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add `exclude` column to storefront access_groups table
 **/
class Migration20170228000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_skus') && !$this->db->tableHasField('#__storefront_skus', 'sCheckoutNotes'))
		{
			$query = "ALTER TABLE `#__storefront_skus`
						ADD `sCheckoutNotes` VARCHAR(255),
						ADD `sCheckoutNotesRequired` tinyint(1) NOT NULL DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_skus') && $this->db->tableHasField('#__storefront_skus', 'sCheckoutNotes'))
		{
			$query = "ALTER TABLE `#__storefront_skus`
			DROP `sCheckoutNotes`,
			DROP `sCheckoutNotesRequired`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
