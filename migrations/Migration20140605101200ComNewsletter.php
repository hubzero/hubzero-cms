<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140605101200ComNewsletter extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		// rename content to html content
		if ($db->tableHasField('#__newsletters', 'content'))
		{
			$query = "ALTER TABLE `#__newsletters` CHANGE `content` `html_content` MEDIUMTEXT;";
			$db->setQuery($query);
			$db->query();
		}

		// add plain text col
		if (!$db->tableHasField('#__newsletters', 'plain_content'))
		{
			$query = "ALTER TABLE `#__newsletters` ADD COLUMN `plain_content` MEDIUMTEXT AFTER `html_content`;";
			$db->setQuery($query);
			$db->query();
		}

		// rename content to html content
		if ($db->tableHasField('#__newsletter_mailings', 'body'))
		{
			$query = "ALTER TABLE `#__newsletter_mailings` CHANGE `body` `html_body` LONGTEXT;";
			$db->setQuery($query);
			$db->query();
		}

		// add plain text col
		if (!$db->tableHasField('#__newsletter_mailings', 'plain_body'))
		{
			$query = "ALTER TABLE `#__newsletter_mailings` ADD COLUMN `plain_body` LONGTEXT AFTER `html_body`;";
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		// rename html content to content
		if ($db->tableHasField('#__newsletters', 'html_content'))
		{
			$query = "ALTER TABLE `#__newsletters` CHANGE `html_content` `content` MEDIUMTEXT;";
			$db->setQuery($query);
			$db->query();
		}

		// remove plain text col
		if ($db->tableHasField('#__newsletters', 'plain_content'))
		{
			$query = "ALTER TABLE `#__newsletters` DROP COLUMN `plain_content`;";
			$db->setQuery($query);
			$db->query();
		}

		// rename html content to content
		if ($db->tableHasField('#__newsletter_mailings', 'html_body'))
		{
			$query = "ALTER TABLE `#__newsletter_mailings` CHANGE `html_body` `body` LONGTEXT;";
			$db->setQuery($query);
			$db->query();
		}

		// remove plain text col
		if ($db->tableHasField('#__newsletter_mailings', 'plain_body'))
		{
			$query = "ALTER TABLE `#__newsletter_mailings` DROP COLUMN `plain_body`;";
			$db->setQuery($query);
			$db->query();
		}
	}
}