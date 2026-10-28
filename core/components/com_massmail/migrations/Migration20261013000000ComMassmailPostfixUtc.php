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
 * The Postfix collector used to bucket log lines by the log's local wall-clock
 * while the dashboard looked them up in UTC, so every stored bucket is keyed
 * off by the host's UTC offset. Clear the rollups and reset the read position
 * so the next collector run re-scans the logs with correct (UTC) keys.
 **/
class Migration20261013000000ComMassmailPostfixUtc extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        foreach (array('#__massmail_postfix_hourly', '#__massmail_postfix_daily') as $table) {
            if ($this->db->tableExists($table)) {
                $this->db->setQuery("TRUNCATE TABLE `$table`;");
                $this->db->query();
            }
        }

        if ($this->db->tableExists('#__massmail_postfix_state')) {
            $this->db->setQuery("UPDATE `#__massmail_postfix_state` SET `last_inode` = 0, `last_offset` = 0, `last_sent` = NULL WHERE `id` = 1;");
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // Nothing to restore: the rollups are rebuilt from the logs.
    }
}
