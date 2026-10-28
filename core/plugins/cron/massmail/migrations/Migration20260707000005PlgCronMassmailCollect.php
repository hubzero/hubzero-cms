<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Massmail\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Seed the Postfix outbound-volume collector cron job. Unlike the sender, this
 * is read-only monitoring (it only reads the mail log and writes rollups), so
 * it is seeded ENABLED so the dashboard volume chart populates out of the box.
 **/
class Migration20260707000005PlgCronMassmailCollect extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__cron_jobs')) {
            $this->db->setQuery(
                "SELECT `id` FROM `#__cron_jobs` WHERE `plugin` = 'massmail' AND `event` = 'collectPostfixVolume';"
            );

            if (!$this->db->loadResult()) {
                $this->db->setQuery(
                    "INSERT INTO `#__cron_jobs`
						(`title`, `state`, `plugin`, `event`, `last_run`, `next_run`, `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`, `params`)
					 VALUES
						('Collect Postfix outbound volume', 1, 'massmail', 'collectPostfixVolume', NULL, UTC_TIMESTAMP(), '*/5 * * * *', NULL, 1, NULL, 0, 0, 0, '');"
                );
                $this->db->query();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if ($this->db->tableExists('#__cron_jobs')) {
            $this->db->setQuery(
                "DELETE FROM `#__cron_jobs` WHERE `plugin` = 'massmail' AND `event` = 'collectPostfixVolume';"
            );
            $this->db->query();
        }
    }
}
