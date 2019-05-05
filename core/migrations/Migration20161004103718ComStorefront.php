<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to add `exclude` column to storefront access_groups table
 **/
class Migration20161004103718ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_product_access_groups') && !$this->db->tableHasField('#__storefront_product_access_groups', 'exclude'))
		{
			$query = "ALTER TABLE `#__storefront_product_access_groups` ADD `exclude` tinyint(2) NOT NULL DEFAULT '0'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_product_access_groups') && $this->db->tableHasField('#__storefront_product_access_groups', 'exclude'))
		{
			$query = "ALTER TABLE `#__storefront_product_access_groups` DROP `exclude`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
