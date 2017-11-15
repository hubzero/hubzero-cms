<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for converting topics plugins to wiki
 **/
class Migration20121009000000ComWiki extends Base
{
	public function up()
	{
		if ($this->db->tableExists('#__plugins'))
		{
			$query = "UPDATE `#__plugins` SET element='wiki' WHERE element='topics';\n";
		}
		else if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET element='wiki' WHERE element='topics';\n";
		}

		$this->db->setQuery($query);
		$this->db->query();
	}
}
