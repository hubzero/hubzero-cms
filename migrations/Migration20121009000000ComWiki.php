<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class Migration20121009000000ComWiki extends Hubzero_Migration
{
	protected static function up($db)
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "UPDATE `#__plugins` SET element='wiki' WHERE element='topics';\n";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET element='wiki' WHERE element='topics';\n";
		}

		$db->setQuery($query);
		$db->query();
	}
}