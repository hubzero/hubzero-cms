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
 * Migration script to add notes field to `#__cart_transaction_info` table
 **/
class Migration20160911000001ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__cart_transaction_info', 'tiNotes'))
		{
			$query = "ALTER TABLE `#__cart_transaction_info` ADD `tiNotes` TEXT  NULL;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__cart_transaction_info', 'tiNotes'))
		{
			$query = "ALTER TABLE `#__cart_transaction_info` DROP COLUMN `tiNotes`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
