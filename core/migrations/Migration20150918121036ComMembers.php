<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding registration reasons table and default values
 *
 */
class Migration20150918121036ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__xprofiles_reasons')) {
            $schema->createTable('#__xprofiles_reasons')
                ->integer('id', ['autoIncrement' => true])
                ->string('reason', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if ($schema->tableExists('#__xprofiles_reasons')) {
            $query = $this->db->getQuery(true)
                ->from('#__xprofiles_reasons');
            $rows = $query->count();

            if (!$rows) {
                $this->db->getQuery(true)
                    ->insert('#__xprofiles_reasons')
                    ->values([
                        ['id' => 1, 'reason' => 'Required for class'],
                        ['id' => 2, 'reason' => 'Developing a new course'],
                        ['id' => 3, 'reason' => 'Using in an existing course'],
                        ['id' => 4, 'reason' => 'Using simulation tools for research'],
                        ['id' => 5, 'reason' => 'Using as background for my research'],
                        ['id' => 6, 'reason' => 'Learning about subject matter'],
                        ['id' => 7, 'reason' => 'Keeping current in subject matter']
                    ])
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__xprofiles_reasons');
    }
}
