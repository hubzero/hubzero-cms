<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Daily rollup of Postfix queue depth (peak + average), so the 30-day outbound
 * chart can overlay a queue line. Queue depth is a live gauge that can't be
 * reconstructed from logs, so this fills in going forward from first sample.
 **/
class Migration20260707000008ComMassmailQueueDaily extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__massmail_queue_daily')) {
            $this->db->setQuery(
                "CREATE TABLE `#__massmail_queue_daily` (
					`day` date NOT NULL,
					`samples` int(11) NOT NULL DEFAULT '0',
					`total` bigint(20) NOT NULL DEFAULT '0',
					`max` int(11) NOT NULL DEFAULT '0',
					PRIMARY KEY (`day`)
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
        if ($this->db->tableExists('#__massmail_queue_daily')) {
            $this->db->setQuery("DROP TABLE `#__massmail_queue_daily`;");
            $this->db->query();
        }
    }
}
