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
 * Scheduled delivery: an optional future time (UTC) at which a launched mailing
 * should begin sending. Until then it sits in the "scheduled" status and the
 * sender leaves it alone.
 **/
class Migration20260710000000ComMassmailSchedule extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (
            $this->db->tableExists('#__massmail_mailings')
            && !$this->db->tableHasField('#__massmail_mailings', 'scheduled_at')
        ) {
            $this->db->setQuery("ALTER TABLE `#__massmail_mailings` ADD COLUMN `scheduled_at` datetime DEFAULT NULL AFTER `status`;");
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if (
            $this->db->tableExists('#__massmail_mailings')
            && $this->db->tableHasField('#__massmail_mailings', 'scheduled_at')
        ) {
            $this->db->setQuery("ALTER TABLE `#__massmail_mailings` DROP COLUMN `scheduled_at`;");
            $this->db->query();
        }
    }
}
