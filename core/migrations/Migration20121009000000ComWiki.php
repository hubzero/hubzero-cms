<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
		else
		{
			$query = "UPDATE `#__extensions` SET element='wiki' WHERE element='topics';\n";
		}

		$this->db->setQuery($query);
		$this->db->query();
	}
}