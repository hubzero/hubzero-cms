<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add Software Download product type to the storefront
 **/
class Migration20160629140839ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_product_types'))
		{
			$this->db->setQuery(
				"SELECT ptId FROM `#__storefront_product_types` WHERE `ptModel`='software'"
			);
			$id = $this->db->loadResult();

			if (!$id)
			{
				$this->db->setQuery(
					"INSERT INTO `#__storefront_product_types` (`ptId`, `ptName`, `ptModel`) VALUES (NULL, 'Software Download', 'software')"
				);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_product_types'))
		{
			$this->db->setQuery(
				"SELECT ptId FROM `#__storefront_product_types` WHERE `ptModel`='software'"
			);
			$id = $this->db->loadResult();

			if ($id)
			{
				$this->db->setQuery(
					"DELETE FROM `#__storefront_product_types` WHERE `ptId`=" . $id
				);
				$this->db->query();
			}
		}
	}
}