<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130716202127ComNewsletter extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		self::addComponentEntry('Newsletters', 'com_newsletter');

		// create component entry
		$query = "";

		//add newsletter table
		$query .= "CREATE TABLE IF NOT EXISTS `#__newsletters` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `alias` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
					  `name` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
					  `issue` int(11) DEFAULT NULL,
					  `type` varchar(50) CHARACTER SET latin1 DEFAULT 'html',
					  `template` int(11) DEFAULT NULL,
					  `published` int(11) DEFAULT '1',
					  `sent` int(11) DEFAULT '0',
					  `content` text CHARACTER SET latin1,
					  `tracking` int(11) DEFAULT '1',
					  `created` datetime DEFAULT NULL,
					  `created_by` int(11) DEFAULT NULL,
					  `modified` datetime DEFAULT NULL,
					  `modified_by` int(11) DEFAULT NULL,
					  `deleted` int(11) DEFAULT '0',
					  `params` text CHARACTER SET latin1,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;
					
					CREATE TABLE IF NOT EXISTS `#__newsletter_templates` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `editable` int(11) DEFAULT '1',
					  `name` varchar(100) DEFAULT NULL,
					  `template` text,
					  `primary_title_color` varchar(100) DEFAULT NULL,
					  `primary_text_color` varchar(100) DEFAULT NULL,
					  `secondary_title_color` varchar(100) DEFAULT NULL,
					  `secondary_text_color` varchar(100) DEFAULT NULL,
					  `deleted` int(11) DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1;
					
					CREATE TABLE IF NOT EXISTS `#__newsletter_primary_story` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `nid` int(11) NOT NULL,
					  `title` varchar(150) DEFAULT NULL,
					  `story` text,
					  `readmore_title` varchar(100) DEFAULT NULL,
					  `readmore_link` varchar(200) DEFAULT NULL,
					  `order` int(11) DEFAULT NULL,
					  `deleted` int(11) DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1;
					
					CREATE TABLE IF NOT EXISTS `#__newsletter_secondary_story` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `nid` int(11) NOT NULL,
					  `title` varchar(150) DEFAULT NULL,
					  `story` text,
					  `readmore_title` varchar(100) DEFAULT NULL,
					  `readmore_link` varchar(200) DEFAULT NULL,
					  `order` int(11) DEFAULT NULL,
					  `deleted` int(11) DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1;
					
					CREATE TABLE IF NOT EXISTS `#__newsletter_mailings` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `nid` int(11) DEFAULT NULL,
					  `lid` int(11) DEFAULT NULL,
					  `subject` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
					  `body` longtext CHARACTER SET latin1,
					  `headers` text CHARACTER SET latin1,
					  `args` text CHARACTER SET latin1,
					  `tracking` int(11) DEFAULT '1',
					  `date` datetime DEFAULT NULL,
					  `deleted` int(11) DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;
					
					CREATE TABLE IF NOT EXISTS `#__newsletter_mailinglists` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `name` varchar(150) DEFAULT NULL,
					  `description` text,
					  `private` int(11) DEFAULT NULL,
					  `deleted` int(11) DEFAULT '0',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1;
					
					CREATE TABLE IF NOT EXISTS `#__newsletter_mailinglist_unsubscribes` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `mid` int(11) DEFAULT NULL,
					  `email` varchar(150) DEFAULT NULL,
					  `reason` text,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1;
					
					CREATE TABLE IF NOT EXISTS `#__newsletter_mailinglist_emails` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `mid` int(11) DEFAULT NULL,
					  `email` varchar(150) DEFAULT NULL,
					  `status` varchar(100) DEFAULT NULL,
					  `confirmed` int(11) DEFAULT '0',
					  `date_added` datetime DEFAULT NULL,
					  `date_confirmed` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1;
					
					CREATE TABLE IF NOT EXISTS `#__newsletter_mailing_recipients` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `mid` int(11) DEFAULT NULL,
					  `email` varchar(150) DEFAULT NULL,
					  `status` varchar(100) DEFAULT NULL,
					  `date_added` datetime DEFAULT NULL,
					  `date_sent` datetime DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;
					
					CREATE TABLE IF NOT EXISTS `#__newsletter_mailing_recipient_actions` (
					  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					  `mailingid` int(11) DEFAULT NULL,
					  `action` varchar(100) DEFAULT NULL,
					  `action_vars` text,
					  `email` varchar(255) DEFAULT NULL,
					  `ip` varchar(100) DEFAULT NULL,
					  `user_agent` varchar(255) DEFAULT NULL,
					  `date` datetime DEFAULT NULL,
					  `countrySHORT` char(2) DEFAULT NULL,
					  `countryLONG` varchar(64) DEFAULT NULL,
					  `ipREGION` varchar(128) DEFAULT NULL,
					  `ipCITY` varchar(128) DEFAULT NULL,
					  `ipLATITUDE` double DEFAULT NULL,
					  `ipLONGITUDE` double DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
					
			// insert default templates
			$query .= "INSERT INTO `#__newsletter_templates` (`editable`, `name`, `template`, `primary_title_color`, `primary_text_color`, `secondary_title_color`, `secondary_text_color`, `deleted`)
				       VALUES (0, 'Default HTML Email Template', '<html>\n	<head>\n		<title>{{TITLE}}</title>\n	</head>\n	<body>\n		<table width=\"100%\" border=\"0\" cellspacing=\"0\">\n			<tr>\n				<td align=\"center\">\n					\n					<table width=\"700\" border=\"0\" cellpadding=\"20\" cellspacing=\"0\">\n						<tr class=\"display-browser\">\n							<td colspan=\"2\" style=\"font-size:10px;padding:0 0 5px 0;\" align=\"center\">\n								Email not displaying correctly? <a href=\"{{LINK}}\">View in a Web Browser</a>\n							</td>\n						</tr>\n						<tr>\n							<td colspan=\"2\" style=\"background:#000000;\">\n								<h1 style=\"color:#FFFFFF;\">HUB Campaign Template</h1>\n								<h3 style=\"color:#888888;\">{{TITLE}}</h3>\n							</td>\n						<tr>\n							<td width=\"500\" valign=\"top\" style=\"font-size:14px;color:#222222;border-left:1px solid #000000;\">\n								<span style=\"display:block;color:#CCCCCC;margin-bottom:20px;\">Issue {{ISSUE}}</span>\n								{{PRIMARY_STORIES}}\n							</td>\n							<td width=\"200\" valign=\"top\" style=\"font-size:12px;color:#555555;border-left:1px solid #AAAAAA;border-right:1px solid #000000;\">\n								{{SECONDARY_STORIES}}\n							</td>\n						</tr>\n						<tr>\n							<td colspan=\"2\" align=\"center\" style=\"background:#000000;color:#FFFFFF;\">\n								Copyright &copy; {{COPYRIGHT}} HUB. All Rights reserved.\n							</td>\n						</tr>\n					</table>\n				\n				</td>\n			</tr>\n		</table>\n	</body>\n</html>	', '', '', '', '', 0);";
				
			$query .= "INSERT INTO `#__newsletter_templates` (`editable`, `name`, `template`, `primary_title_color`, `primary_text_color`, `secondary_title_color`, `secondary_text_color`, `deleted`)
				       VALUES
					(0, 'Default Plain Text Email Template', 'View In Browser - {{LINK}}\n=====================================\n{{TITLE}} - {{ISSUE}}\n=====================================\n\n{{PRIMARY_STORIES}}\n\n--------------------------------------------------\n\n{{SECONDARY_STORIES}}\n\n--------------------------------------------------\n\nUnsubscribe - {{UNSUBSCRIBE_LINK}}\nCopyright - {{COPYRIGHT}}', NULL, NULL, NULL, NULL, 0);";

			//add newsletter cron jobs
			$query .= "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`)
						SELECT 'Process Newsletter Mailings', 0, 'newsletter', 'processMailings', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '*/5 * * * *', '2013-06-25 08:23:04', 1001, '2013-07-16 17:15:01', 0, 0, 0, 'newsletter_queue_limit=2\nsupport_ticketreminder_severity=all\nsupport_ticketreminder_group=\n\n'
						FROM DUAL WHERE NOT EXISTS (SELECT `title` FROM `#__cron_jobs` WHERE `title` = 'Process Newsletter Mailings');";
						
			$query .= "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`)
						SELECT 'Process Newsletter Opens & Click IP Addresses', 0, 'newsletter', 'processIps', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '*/5 * * * *', '2013-06-25 08:23:04', 1001, '2013-07-16 17:15:01', 0, 0, 0, ''
						FROM DUAL WHERE NOT EXISTS (SELECT `title` FROM `#__cron_jobs` WHERE `title` = 'Process Newsletter Opens & Click IP Addresses');";
				
		
		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		self::addPluginEntry('cron', 'newsletter');
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deleteComponentEntry('Newsletters');
		
		// remove all newsletter tables
		$query .= "
			DROP TABLE IF EXISTS `#__newsletters`;
			DROP TABLE IF EXISTS `#__newsletter_templates`;
			DROP TABLE IF EXISTS `#__newsletter_primary_story`;
			DROP TABLE IF EXISTS `#__newsletter_secondary_story`;
			DROP TABLE IF EXISTS `#__newsletter_mailings`;
			DROP TABLE IF EXISTS `#__newsletter_mailinglists`;
			DROP TABLE IF EXISTS `#__newsletter_mailinglist_unsubscribes`;
			DROP TABLE IF EXISTS `#__newsletter_mailinglist_emails`;
			DROP TABLE IF EXISTS `#__newsletter_mailing_recipients`;
			DROP TABLE IF EXISTS `#__newsletter_mailing_recipient_actions`;";
		
		//remove newsletter cron jobs 
		$query .= "DELETE FROM `#__cron_jobs` WHERE `title`='Process Newsletter Mailings';";
		$query .= "DELETE FROM `#__cron_jobs` WHERE `title`='Process Newsletter Opens & Click IP Addresses';";
		
		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}

		self::deletePluginEntry('cron', 'newsletter');
	}
}