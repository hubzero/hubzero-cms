<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for com_massmail: register the component and create tables.
 **/
class Migration20260707000000ComMassmail extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addComponentEntry('massmail');

        if (!$this->db->tableExists('#__massmail_mailings')) {
            $this->db->setQuery(
                "CREATE TABLE `#__massmail_mailings` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`subject` varchar(255) NOT NULL DEFAULT '',
					`body` longtext,
					`body_html` longtext,
					`body_text` longtext,
					`from_name` varchar(255) NOT NULL DEFAULT '',
					`from_email` varchar(255) NOT NULL DEFAULT '',
					`reply_to` varchar(255) NOT NULL DEFAULT '',
					`recipient_spec` text,
					`ignore_optout` tinyint(2) NOT NULL DEFAULT '0',
					`status` varchar(32) NOT NULL DEFAULT 'draft',
					`total_recipients` int(11) NOT NULL DEFAULT '0',
					`sent_count` int(11) NOT NULL DEFAULT '0',
					`failed_count` int(11) NOT NULL DEFAULT '0',
					`skipped_count` int(11) NOT NULL DEFAULT '0',
					`created` datetime DEFAULT NULL,
					`created_by` int(11) NOT NULL DEFAULT '0',
					`approved_by` int(11) NOT NULL DEFAULT '0',
					`approved_on` datetime DEFAULT NULL,
					`started_on` datetime DEFAULT NULL,
					`completed_on` datetime DEFAULT NULL,
					`params` text,
					PRIMARY KEY (`id`),
					KEY `idx_status` (`status`),
					KEY `idx_created_by` (`created_by`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
            );
            $this->db->query();
        }

        if (!$this->db->tableExists('#__massmail_recipients')) {
            $this->db->setQuery(
                "CREATE TABLE `#__massmail_recipients` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`mailing_id` int(11) unsigned NOT NULL DEFAULT '0',
					`uid` int(11) NOT NULL DEFAULT '0',
					`email` varchar(150) NOT NULL DEFAULT '',
					`name` varchar(255) NOT NULL DEFAULT '',
					`status` tinyint(2) NOT NULL DEFAULT '0',
					`attempts` tinyint(3) NOT NULL DEFAULT '0',
					`sent_on` datetime DEFAULT NULL,
					`error` varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY (`id`),
					KEY `idx_mailing_status` (`mailing_id`,`status`),
					KEY `idx_sent_on` (`sent_on`),
					UNIQUE KEY `uniq_mailing_email` (`mailing_id`,`email`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
            );
            $this->db->query();
        }

        if (!$this->db->tableExists('#__massmail_attachments')) {
            $this->db->setQuery(
                "CREATE TABLE `#__massmail_attachments` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`mailing_id` int(11) unsigned NOT NULL DEFAULT '0',
					`filename` varchar(255) NOT NULL DEFAULT '',
					`stored_path` varchar(255) NOT NULL DEFAULT '',
					`size` int(11) NOT NULL DEFAULT '0',
					`mimetype` varchar(150) NOT NULL DEFAULT '',
					`created` datetime DEFAULT NULL,
					PRIMARY KEY (`id`),
					KEY `idx_mailing` (`mailing_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
            );
            $this->db->query();
        }

        if (!$this->db->tableExists('#__massmail_status')) {
            $this->db->setQuery(
                "CREATE TABLE `#__massmail_status` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`queue_size` int(11) NOT NULL DEFAULT '0',
					`sent_last_hour` int(11) NOT NULL DEFAULT '0',
					`last_checked` datetime DEFAULT NULL,
					`last_run` datetime DEFAULT NULL,
					`note` varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
            );
            $this->db->query();

            // Seed the single status row.
            $this->db->setQuery("INSERT INTO `#__massmail_status` (`id`) VALUES (1);");
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteComponentEntry('massmail');

        foreach (array('#__massmail_recipients', '#__massmail_attachments', '#__massmail_mailings', '#__massmail_status') as $table) {
            if ($this->db->tableExists($table)) {
                $this->db->setQuery("DROP TABLE `$table`;");
                $this->db->query();
            }
        }
    }
}
