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
 * Lightweight time series of Postfix queue depth, sampled by the (always-on)
 * collector cron. Queue depth is a gauge whose current value is what matters
 * operationally (shown live and used for backpressure); this short-retention
 * series just lets the dashboard show a recent trend and 24h peak so a
 * transient backlog isn't missed. No long-term rollup -- depth is near zero
 * except during incidents.
 **/
class Migration20260707000006ComMassmailQueue extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__massmail_queue_samples')) {
            $this->db->setQuery(
                "CREATE TABLE `#__massmail_queue_samples` (
					`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`recorded` datetime DEFAULT NULL,
					`depth` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`id`),
					KEY `idx_recorded` (`recorded`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
            );
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if ($this->db->tableExists('#__massmail_queue_samples')) {
            $this->db->setQuery("DROP TABLE `#__massmail_queue_samples`;");
            $this->db->query();
        }
    }
}
