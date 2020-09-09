<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing storefront tables
 **/
class Migration20200908000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_product_types'))
		{
			$query = "INSERT INTO `#__storefront_product_types` (`ptName`,`ptModel`)
							VALUES ('Group Membership', 'membership')";

			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log(sprintf('Created new Group membership product type'));
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
			$query = "DELETE FROM `#__storefront_product_types` WHERE (`ptName` = 'Group Membership')";

			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log(sprintf('Removed Group membership product type'));
			}
		}
	}
}
