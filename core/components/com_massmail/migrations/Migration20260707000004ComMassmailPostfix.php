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
 * Tables for the Postfix outbound-volume collector: hourly and daily rollups
 * of messages that left the host, plus a small state row tracking how far into
 * the mail log we have read (inode + byte offset) for incremental collection.
 **/
class Migration20260707000004ComMassmailPostfix extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__massmail_postfix_hourly')) {
            $this->db->setQuery(
                "CREATE TABLE `#__massmail_postfix_hourly` (
					`hour` datetime NOT NULL,
					`sent` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`hour`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
            );
            $this->db->query();
        }

        if (!$this->db->tableExists('#__massmail_postfix_daily')) {
            $this->db->setQuery(
                "CREATE TABLE `#__massmail_postfix_daily` (
					`day` date NOT NULL,
					`sent` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`day`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
            );
            $this->db->query();
        }

        if (!$this->db->tableExists('#__massmail_postfix_state')) {
            $this->db->setQuery(
                "CREATE TABLE `#__massmail_postfix_state` (
					`id` int(11) unsigned NOT NULL,
					`last_inode` bigint(20) NOT NULL DEFAULT '0',
					`last_offset` bigint(20) NOT NULL DEFAULT '0',
					`last_run` datetime DEFAULT NULL,
					PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
            );
            $this->db->query();

            $this->db->setQuery("INSERT INTO `#__massmail_postfix_state` (`id`) VALUES (1);");
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        foreach (array('#__massmail_postfix_hourly', '#__massmail_postfix_daily', '#__massmail_postfix_state') as $table) {
            if ($this->db->tableExists($table)) {
                $this->db->setQuery("DROP TABLE `$table`;");
                $this->db->query();
            }
        }
    }
}
