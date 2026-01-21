<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding ratelimit memory table
  *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20250305102831Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__ratelimit')) {
            $query = "CREATE TABLE `#__ratelimit` (
  `ratelimit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varbinary(16) NOT NULL,
  `rule_id` int(10) unsigned NOT NULL DEFAULT 0,
  `count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `window_sz` int(10) unsigned NOT NULL DEFAULT 1,
  `last_ts` bigint(20) unsigned NOT NULL DEFAULT 0,
  `last_cnt` int(10) unsigned NOT NULL DEFAULT 0,
  `current_ts` bigint(20) unsigned NOT NULL DEFAULT 0,
  `current_cnt` int(10) unsigned NOT NULL DEFAULT 0,
  `triggered` int(10) unsigned NOT NULL DEFAULT 0,
  `action` varchar(255) NOT NULL DEFAULT 'log',
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ratelimit_id`),
  UNIQUE KEY `uidx_ip_rule_id` (`ip`,`rule_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

            $this->db->setQuery($query);
            $this->db->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if (!$this->db->tableExists('#__ratelimit')) {
            $query = "DROP TABLE #__ratelimit;";
            $this->db->setQuery($query);
            $this->db->execute();
        }
    }
}
