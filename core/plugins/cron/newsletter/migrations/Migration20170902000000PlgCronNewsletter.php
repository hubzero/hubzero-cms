<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing default newsletter cron jobs
 **/
class Migration20170902000000PlgCronNewsletter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if ($this->db->tableExists('#__cron_jobs')) {
            $query = "SELECT `id` FROM `#__cron_jobs` WHERE `plugin`='newsletter' AND `event`='processMailings';";
            $this->db->setQuery($query);
            $id = $this->db->loadResult();

            if (!$id) {
                $query = "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`,"
                    . " `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`,"
                    . " `params`) VALUES ('Process Newsletter Mailings', 0, 'newsletter', 'processMailings',"
                    . " '0000-00-00 00:00:00', '0000-00-00 00:00:00', '*/5 * * * *', NOW(), 0, NOW(), 0, 0, 0, '');";

                $this->db->setQuery($query);
                $this->db->query();
            }

            $query = "SELECT `id` FROM `#__cron_jobs` WHERE `plugin`='newsletter' AND `event`='processIps';";
            $this->db->setQuery($query);
            $id = $this->db->loadResult();

            if (!$id) {
                $query = "INSERT INTO `#__cron_jobs` (`title`, `state`, `plugin`, `event`, `last_run`, `next_run`,"
                    . " `recurrence`, `created`, `created_by`, `modified`, `modified_by`, `active`, `ordering`,"
                    . " `params`) VALUES ('Process Newsletter Opens & Click IP Addresses', 0, 'newsletter',"
                    . " 'processIps', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '*/5 * * * *', NOW(), 0, NOW(),"
                    . " 0, 0, 0, '');";

                $this->db->setQuery($query);
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
            $query = "DELETE FROM `#__cron_jobs` WHERE `plugin`='newsletter'"
                . " AND `event` IN ('processMailings', 'processIps');";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
