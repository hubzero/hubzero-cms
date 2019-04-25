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
 * Migration script to add AUTO_INCREMENT to cId
 **/
class Migration20150924000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_collections') && $this->db->tableHasField('#__storefront_collections', 'cId'))
		{
			$query = "ALTER TABLE `#__storefront_collections` MODIFY COLUMN `cId` INT(16) UNSIGNED NOT NULL AUTO_INCREMENT";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
