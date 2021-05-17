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
class Migration20210430000001ComStorefront extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__storefront_products') && !$this->db->tableHasField('#__storefront_products', 'pExternalCheckoutURL'))
		{
			$query = "ALTER TABLE `#__storefront_products` 
			ADD COLUMN IF NOT EXISTS pExternalCheckoutURL VARCHAR(2083) AFTER pAllowMultiple,
			ADD COLUMN IF NOT EXISTS pExternalCheckoutProvider VARCHAR(15) AFTER pAllowMultiple,
			ADD COLUMN IF NOT EXISTS pExternalCheckoutID VARCHAR(100) AFTER pAllowMultiple
			";

			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log(sprintf('Added product external checkout DB support'));
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__storefront_products') && $this->db->tableHasField('#__storefront_products', 'pExternalCheckoutURL'))
		{
			$query = "ALTER TABLE `#__storefront_products` 
			DROP COLUMN pExternalCheckoutURL,
			DROP COLUMN pExternalCheckoutProvider,
			DROP COLUMN pExternalCheckoutID
			";

			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log(sprintf('Removed product external checkout DB support'));
			}
		}
	}
}
