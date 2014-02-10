<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140113135231ComSearch extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_search' AND `protected`=1;";

		$db->setQuery($query);

		if ($id = $db->loadResult())
		{
			self::deleteComponentEntry('search');

			self::deletePluginEntry('search');

			$query = "UPDATE `#__extensions` SET `element`='com_search', `name`='Search' WHERE `type`='component' AND `element`='com_ysearch';";
			$db->setQuery($query);
			$db->query();

			$query = "UPDATE `#__menu` SET `title`='com_search', `alias`='search', `path`='search', `link`='index.php?option=com_search&task=configure' WHERE `title`='com_ysearch';";
			$db->setQuery($query);
			$db->query();

			$query = "UPDATE `#__extensions` SET `folder`='search' WHERE `folder`='ysearch' AND `type`='plugin';";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='component' AND `element`='com_search' AND `protected`=0;";

		$db->setQuery($query);

		if ($id = $db->loadResult())
		{
			$query = "UPDATE `#__extensions` SET `element`='com_ysearch', `name`='YSearch' WHERE `type`='component' AND `element`='com_search' AND `protected`=0;";
			$db->setQuery($query);
			$db->query();

			$query = "UPDATE `#__extensions` SET `folder`='ysearch' WHERE `folder`='search' AND `type`='plugin';";
			$db->setQuery($query);
			$db->query();

			$query = "UPDATE `#__menu` SET `title`='com_ysearch', `alias`='ysearch', `path`='ysearch', `link`='index.php?option=com_ysearch&task=configure' WHERE `title`='com_search';";
			$db->setQuery($query);
			$db->query();

			self::addComponentEntry('search');

			$query = "UPDATE `#__extensions` SET `protected`=1 WHERE `type`='component' AND `element`='com_search';";
			$db->setQuery($query);
			$db->query();
		}
	}
}