<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for tracking group announcement emails
  *
**/
class Migration20131016184016PlgGroupsAnnouncements extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // add email column
        if (!$schema->hasColumn('#__announcements', 'email')) {
            $schema->addColumn('#__announcements', 'email')->tinyInteger(4)->default(0);
        }

        // add sent column
        if (!$schema->hasColumn('#__announcements', 'sent')) {
            $schema->addColumn('#__announcements', 'sent')->tinyInteger(4)->default(0);
        }

        // check for group announcements cron
        $query = $this->db->getQuery(true)
            ->select('title')
            ->from('#__cron_jobs')
            ->where('title', '=', 'Group Announcements');

        if ($query->doesntExist()) {
            // add group announcements cron
            $this->db->getQuery(true)
                ->insertOrIgnore('#__cron_jobs')
                ->values([
                    'title'      => 'Group Announcements',
                    'state'      => 1,
                    'plugin'     => 'groups',
                    'event'      => 'sendGroupAnnouncements',
                    'recurrence' => '*/5 * * * *'
                ])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // remove email column
        if ($schema->hasColumn('#__announcements', 'email')) {
            $schema->dropColumn('#__announcements', 'email');
        }

        // remove sent column
        if ($schema->hasColumn('#__announcements', 'sent')) {
            $schema->dropColumn('#__announcements', 'sent');
        }

        // remove announcements cron
        $this->db->getQuery(true)
            ->delete('#__cron_jobs')
            ->where('event', '=', 'sendGroupAnnouncements')
            ->execute();
    }
}
