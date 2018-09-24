<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration to add primary key to display table
 **/

class Migration20180924160808Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('display'))
		{
			$keys = $this->db->getTableKeys('display');
			$primary_keys = array();
			if (is_array($keys) && (count($keys) > 0))
			{
				foreach ($keys as $k)
				{
					if ($k->Key_name == 'PRIMARY')
					{
						$primary_keys[] = $k->Column_name;
					}

				}
			}
			if (count($primary_keys) == 0)
			{
				$query = "ALTER TABLE `display` ADD PRIMARY KEY (hostname, dispnum)";
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
		if ($this->db->tableExists('display'))
		{
			if ($this->db->getPrimaryKey('display') == 'hostname' || $this->db->getPrimaryKey('display') == 'dispnum')
			{
				$query = "ALTER TABLE `display` DROP PRIMARY KEY";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
