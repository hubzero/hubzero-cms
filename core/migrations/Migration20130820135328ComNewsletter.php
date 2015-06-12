<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing newletter character set
 **/
class Migration20130820135328ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
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
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}