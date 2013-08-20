<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130820135328ComNewsletter extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "
			ALTER TABLE `#__newsletters` CONVERT TO CHARACTER SET utf8;
			ALTER TABLE `#__newsletter_templates` CONVERT TO CHARACTER SET utf8;
			ALTER TABLE `#__newsletter_secondary_story` CONVERT TO CHARACTER SET utf8;
			ALTER TABLE `#__newsletter_primary_story` CONVERT TO CHARACTER SET utf8;
			ALTER TABLE `#__newsletter_mailings` CONVERT TO CHARACTER SET utf8;
			ALTER TABLE `#__newsletter_mailinglists` CONVERT TO CHARACTER SET utf8;
			ALTER TABLE `#__newsletter_mailinglist_unsubscribes` CONVERT TO CHARACTER SET utf8;
			ALTER TABLE `#__newsletter_mailinglist_emails` CONVERT TO CHARACTER SET utf8;
			ALTER TABLE `#__newsletter_mailing_recipients` CONVERT TO CHARACTER SET utf8;
			ALTER TABLE `#__newsletter_mailing_recipient_actions` CONVERT TO CHARACTER SET utf8;";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}