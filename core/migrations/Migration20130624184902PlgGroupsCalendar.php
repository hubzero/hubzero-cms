<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding group calendar functionality
  *
**/
class Migration20130624184902PlgGroupsCalendar extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->hasColumn('#__events', 'ical_uid')) {
            $schema->addColumn('#__events', 'ical_uid')->string()->after('calendar_id')->execute();
        }

        if (!$schema->hasColumn('#__events_calendars', 'url')) {
            $schema->addColumn('#__events_calendars', 'url')->string()->execute();
        }

        if (!$schema->hasColumn('#__events_calendars', 'readonly')) {
            $schema->addColumn('#__events_calendars', 'readonly')->tinyInteger()->default(0)->execute();
        }

        if (!$schema->hasColumn('#__events_calendars', 'last_fetched')) {
            $schema->addColumn('#__events_calendars', 'last_fetched')->datetime()->execute();
        }

        if (!$schema->hasColumn('#__events_calendars', 'last_fetched_attempt')) {
            $schema->addColumn('#__events_calendars', 'last_fetched_attempt')->datetime()->execute();
        }

        if (!$schema->hasColumn('#__events_calendars', 'failed_attempts')) {
            $schema->addColumn('#__events_calendars', 'failed_attempts')->integer()->default(0)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__events', 'ical_uid')) {
            $schema->dropColumn('#__events', 'ical_uid');
        }

        if ($schema->hasColumn('#__events_calendars', 'url')) {
            $schema->dropColumn('#__events_calendars', 'url');
        }

        if ($schema->hasColumn('#__events_calendars', 'readonly')) {
            $schema->dropColumn('#__events_calendars', 'readonly');
        }

        if ($schema->hasColumn('#__events_calendars', 'last_fetched')) {
            $schema->dropColumn('#__events_calendars', 'last_fetched');
        }

        if ($schema->hasColumn('#__events_calendars', 'last_fetched_attempt')) {
            $schema->dropColumn('#__events_calendars', 'last_fetched_attempt');
        }

        if ($schema->hasColumn('#__events_calendars', 'failed_attempts')) {
            $schema->dropColumn('#__events_calendars', 'failed_attempts');
        }
    }
}
