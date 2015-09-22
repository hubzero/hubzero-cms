<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for dropping the steps index when it was named automatically by the database and the name didn't match the previous migration
 **/
class Migration20151030000001ComCart extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__cart_transaction_steps'))
		{
			$query = "SELECT DISTINCT INDEX_NAME FROM information_schema.statistics WHERE table_name = '" . $this->db->replacePrefix('#__cart_transaction_steps') . "' AND INDEX_NAME LIKE '%tId%'";
			$this->db->setQuery($query);
			$this->db->execute();
			if ($this->db->getNumRows() > 0)
			{
				$indexname = $this->db->loadResult();

				$query = "SHOW INDEX FROM " . $this->db->replacePrefix('#__cart_transaction_steps') . " WHERE Key_name = '{$indexname}'";
				$this->db->setQuery($query);
				$this->db->execute();
				if ($this->db->getNumRows() > 0)
				{
					$query = "DROP INDEX `{$indexname}` ON " . $this->db->replacePrefix('#__cart_transaction_steps');
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// Change tsMeta to 255 chars long
			$query = "ALTER TABLE `#__cart_transaction_steps` MODIFY `tsMeta` CHAR(255)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function down()
	{

	}

}