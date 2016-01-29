<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indexes to Newsletter tables
 **/
class Migration20160129154900ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__newsletter_mailing_recipient_actions'))
		{
			if (!$this->db->tableHasKey('#__newsletter_mailing_recipient_actions', 'idx_mailingid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailing_recipient_actions` ADD INDEX `idx_mailingid` (`mailingid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletter_mailing_recipient_actions', 'idx_action'))
			{
				$query = "ALTER TABLE `#__newsletter_mailing_recipient_actions` ADD INDEX `idx_action` (`action`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailing_recipients'))
		{
			if (!$this->db->tableHasKey('#__newsletter_mailing_recipients', 'idx_mid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailing_recipients` ADD INDEX `idx_mid` (`mid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletter_mailing_recipients', 'idx_status'))
			{
				$query = "ALTER TABLE `#__newsletter_mailing_recipients` ADD INDEX `idx_status` (`status`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailinglist_emails'))
		{
			if (!$this->db->tableHasKey('#__newsletter_mailinglist_emails', 'idx_mid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglist_emails` ADD INDEX `idx_mid` (`mid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletter_mailinglist_emails', 'idx_status'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglist_emails` ADD INDEX `idx_status` (`status`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailinglist_unsubscribes'))
		{
			if (!$this->db->tableHasKey('#__newsletter_mailinglist_unsubscribes', 'idx_mid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglist_unsubscribes` ADD INDEX `idx_mid` (`mid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailinglists'))
		{
			if (!$this->db->tableHasKey('#__newsletter_mailinglists', 'idx_private'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglists` ADD INDEX `idx_private` (`private`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletter_mailinglists', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglists` ADD INDEX `idx_deleted` (`deleted`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailings'))
		{
			if (!$this->db->tableHasKey('#__newsletter_mailings', 'idx_nid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailings` ADD INDEX `idx_nid` (`nid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletter_mailings', 'idx_lid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailings` ADD INDEX `idx_lid` (`lid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletter_mailings', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletter_mailings` ADD INDEX `idx_deleted` (`deleted`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_primary_story'))
		{
			if (!$this->db->tableHasKey('#__newsletter_primary_story', 'idx_nid'))
			{
				$query = "ALTER TABLE `#__newsletter_primary_story` ADD INDEX `idx_nid` (`nid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletter_primary_story', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletter_primary_story` ADD INDEX `idx_deleted` (`deleted`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_secondary_story'))
		{
			if (!$this->db->tableHasKey('#__newsletter_secondary_story', 'idx_nid'))
			{
				$query = "ALTER TABLE `#__newsletter_secondary_story` ADD INDEX `idx_nid` (`nid`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletter_secondary_story', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletter_secondary_story` ADD INDEX `idx_deleted` (`deleted`);";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletters'))
		{
			if (!$this->db->tableHasKey('#__newsletters', 'idx_published'))
			{
				$query = "ALTER TABLE `#__newsletters` ADD INDEX `idx_published` (`published`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletters', 'idx_sent'))
			{
				$query = "ALTER TABLE `#__newsletters` ADD INDEX `idx_sent` (`sent`);";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__newsletters', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletters` ADD INDEX `idx_deleted` (`deleted`);";
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
		if ($this->db->tableExists('#__newsletter_mailing_recipient_actions'))
		{
			if ($this->db->tableHasKey('#__newsletter_mailing_recipient_actions', 'idx_mailingid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailing_recipient_actions` DROP INDEX `idx_mailingid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletter_mailing_recipient_actions', 'idx_action'))
			{
				$query = "ALTER TABLE `#__newsletter_mailing_recipient_actions` DROP INDEX `idx_action`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailing_recipients'))
		{
			if ($this->db->tableHasKey('#__newsletter_mailing_recipients', 'idx_mid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailing_recipients` DROP INDEX `idx_mid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletter_mailing_recipients', 'idx_status'))
			{
				$query = "ALTER TABLE `#__newsletter_mailing_recipients` DROP INDEX `idx_status`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailinglist_emails'))
		{
			if ($this->db->tableHasKey('#__newsletter_mailinglist_emails', 'idx_mid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglist_emails` DROP INDEX `idx_mid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletter_mailinglist_emails', 'idx_status'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglist_emails` DROP INDEX `idx_status`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailinglist_unsubscribes'))
		{
			if ($this->db->tableHasKey('#__newsletter_mailinglist_unsubscribes', 'idx_mid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglist_unsubscribes` DROP INDEX `idx_mid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailinglists'))
		{
			if ($this->db->tableHasKey('#__newsletter_mailinglists', 'idx_private'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglists` DROP INDEX `idx_private`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletter_mailinglists', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletter_mailinglists` DROP INDEX `idx_deleted`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_mailings'))
		{
			if ($this->db->tableHasKey('#__newsletter_mailings', 'idx_nid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailings` DROP INDEX `idx_nid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletter_mailings', 'idx_lid'))
			{
				$query = "ALTER TABLE `#__newsletter_mailings` DROP INDEX `idx_lid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletter_mailings', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletter_mailings` DROP INDEX `idx_deleted`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_primary_story'))
		{
			if ($this->db->tableHasKey('#__newsletter_primary_story', 'idx_nid'))
			{
				$query = "ALTER TABLE `#__newsletter_primary_story` DROP INDEX `idx_nid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletter_primary_story', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletter_primary_story` DROP INDEX `idx_deleted`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletter_secondary_story'))
		{
			if ($this->db->tableHasKey('#__newsletter_secondary_story', 'idx_nid'))
			{
				$query = "ALTER TABLE `#__newsletter_secondary_story` DROP INDEX `idx_nid`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletter_secondary_story', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletter_secondary_story` DROP INDEX `idx_deleted`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__newsletters'))
		{
			if ($this->db->tableHasKey('#__newsletters', 'idx_published'))
			{
				$query = "ALTER TABLE `#__newsletters` DROP INDEX `idx_published`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletters', 'idx_sent'))
			{
				$query = "ALTER TABLE `#__newsletters` DROP INDEX `idx_sent`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasKey('#__newsletters', 'idx_deleted'))
			{
				$query = "ALTER TABLE `#__newsletters` DROP INDEX `idx_deleted`;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}