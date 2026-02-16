<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for populating newletter cron job
**/
class Migration20130717140704PlgCronNewsletter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        /**
         * Forgot to re-add migration script after adding it to git
         * staging and making further changes. this will make sure
         * newsletter cron jobs exits.
         */
        $query = "";

        //add newsletter cron jobs
        $params1 = 'newsletter_queue_limit=2\nsupport_ticketreminder_severity=all\nsupport_ticketreminder_group=\n\n';

        //add newsletter cron jobs
        $params1 = 'newsletter_queue_limit=2\nsupport_ticketreminder_severity=all\nsupport_ticketreminder_group=\n\n';

        $query = $this->db->getQuery(true)
            ->select('title')
            ->from('#__cron_jobs')
            ->where('title', '=', 'Process Newsletter Mailings');

        if ($query->doesntExist()) {
            $this->db->getQuery(true)
                ->insertOrIgnore('#__cron_jobs')
                ->set([
                    'title'      => 'Process Newsletter Mailings',
                    'state'      => 0,
                    'plugin'     => 'newsletter',
                    'event'      => 'processMailings',
                    'last_run'   => '0000-00-00 00:00:00',
                    'next_run'   => '0000-00-00 00:00:00',
                    'recurrence' => '*/5 * * * *',
                    'created'    => '2013-06-25 08:23:04',
                    'created_by' => 1001,
                    'modified'   => '2013-07-16 17:15:01',
                    'modified_by' => 0,
                    'active'     => 0,
                    'ordering'   => 0,
                    'params'     => $params1
                ])
                ->execute();
        }

        $query = $this->db->getQuery(true)
            ->select('title')
            ->from('#__cron_jobs')
            ->where('title', '=', 'Process Newsletter Opens & Click IP Addresses');

        if ($query->doesntExist()) {
            $this->db->getQuery(true)
                ->insertOrIgnore('#__cron_jobs')
                ->set([
                    'title'      => 'Process Newsletter Opens & Click IP Addresses',
                    'state'      => 0,
                    'plugin'     => 'newsletter',
                    'event'      => 'processIps',
                    'last_run'   => '0000-00-00 00:00:00',
                    'next_run'   => '0000-00-00 00:00:00',
                    'recurrence' => '*/5 * * * *',
                    'created'    => '2013-06-25 08:23:04',
                    'created_by' => 1001,
                    'modified'   => '2013-07-16 17:15:01',
                    'modified_by' => 0,
                    'active'     => 0,
                    'ordering'   => 0,
                    'params'     => ''
                ])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        //remove newsletter cron jobs
        $this->db->getQuery(true)
            ->delete('#__cron_jobs')
            ->whereIn('title', ['Process Newsletter Mailings', 'Process Newsletter Opens & Click IP Addresses'])
            ->execute();
    }
}
