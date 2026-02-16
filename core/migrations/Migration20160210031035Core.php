<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding a status column to the migrations table
**/
class Migration20160210031035Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__migrations')
            && !$schema->hasColumn('#__migrations', 'status')
            && $schema->hasColumn('#__migrations', 'action_by')
        ) {
            $schema->addColumn('#__migrations', 'status')
                ->string(255)
                ->notNull()
                ->default('')
                ->after('action_by')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__migrations')
                ->set(['status' => 'success'])
                ->execute();
        }
    }

    /**
     * Down
     **/
    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__migrations') && $schema->hasColumn('#__migrations', 'status')) {
            // We delete all non success entries - this loses data, but it
            // restores the original intent of the migrations table.
            // Otherwise, you'd have entries in the table that will look like
            // successful runs, even though they weren't.
            $this->db->getQuery(true)
                ->delete('#__migrations')
                ->where('status', '!=', 'success')
                ->execute();

            $schema->dropColumn('#__migrations', 'status');
        }
    }
}
