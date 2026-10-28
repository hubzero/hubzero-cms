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
 * The cron runner only picks jobs whose next_run has arrived, and the seed
 * migrations inserted the mass mail jobs with next_run NULL -- so the Postfix
 * collector, enabled from the start, never ran. Give any enabled massmail job
 * that still has no next_run one now; the runner advances it from there.
 **/
class Migration20261013000001PlgCronMassmailNextRun extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__cron_jobs')) {
            $this->db->setQuery(
                "UPDATE `#__cron_jobs` SET `next_run` = UTC_TIMESTAMP()
				 WHERE `plugin` = 'massmail' AND `state` = 1 AND `next_run` IS NULL;"
            );
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // Nothing to undo: next_run is runtime state the runner keeps advancing.
    }
}
