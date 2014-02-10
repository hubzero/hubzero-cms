<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing some links in default content
 **/
class Migration20130426071400Core extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "SELECT `introtext` FROM `#__content` WHERE alias='licensing' AND title='Intellectual Property Considerations';";
		$db->setQuery($query);
		$result = $db->loadResult();

		$result = str_replace('<a href="http://www.hubzero.org/topics/middleware">unique middleware</a>', 'unique middleware', $result);

		$query = "UPDATE `#__content` SET introtext=".$db->Quote($result)." WHERE alias='licensing' AND title='Intellectual Property Considerations' LIMIT 1;
					UPDATE `#__content` SET introtext=REPLACE(introtext,'/feedback/report_problems/','/support/ticket/new') WHERE alias='licensing' AND title='Intellectual Property Considerations' LIMIT 1;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}