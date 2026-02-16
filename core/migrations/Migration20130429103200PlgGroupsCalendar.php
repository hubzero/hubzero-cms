<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for new group calendar plugin
 *
*/
class Migration20130429103200PlgGroupsCalendar extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $query = '';

        // create event calendars table
        if (!$schema->tableExists('#__events_calendars')) {
            $schema->createTable('#__events_calendars')
                ->id()
                ->string('scope', 100)->nullable()
                ->integer('scope_id')->nullable()
                ->string('title', 100)->nullable()
                ->string('color', 100)->nullable()
                ->integer('published')->default(1)
                ->execute();
        }

        // add calendar_id, scope, and scope id to events so we can have them belong to other sections
        if (!$schema->hasColumn('#__events', 'calendar_id')) {
            $schema->addColumn('#__events', 'calendar_id')->integer(11);
        }
        if (!$schema->hasColumn('#__events', 'scope')) {
            $schema->addColumn('#__events', 'scope')
                ->string(100)
                ->execute();

            // set scope on all current site events
            $this->db->getQuery(true)
                ->update('#__events')
                ->set(['scope' => 'event'])
                ->beginOrGroup()
                    ->whereIsNull('scope')
                    ->orWhere('scope', '=', '')
                ->endAndGroup()
                ->execute();
        }
        if (!$schema->hasColumn('#__events', 'scope_id')) {
            $schema->addColumn('#__events', 'scope_id')->integer(11);
        }

        if ($schema->tableExists('#__xgroups_events')) {
            // move group events to events table
            $select = $this->db->getQuery(true);
            $select->select($this->db->quote('group'))
                ->select('gidNumber', 'scope_id')
                ->select('title')
                ->select('details', 'content')
                ->select('active', 'state')
                ->select('created')
                ->select('actorid', 'created_by')
                ->select('start', 'publish_up')
                ->select('end', 'publish_down')
                ->from('#__xgroups_events');

            $this->db->getQuery(true)
                ->insert('#__events')
                ->columns([
                    'scope',
                    'scope_id',
                    'title',
                    'content',
                    'state',
                    'created',
                    'created_by',
                    'publish_up',
                    'publish_down',
                ])
                ->fromSelect($select)
                ->execute();

            // drop group events table
            $schema->dropTable('#__xgroups_events');
        }
    }
}
