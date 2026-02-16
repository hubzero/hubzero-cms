<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to add table for tracking activity digest settings
 *
*/
class Migration20160802170610PlgMembersActivity extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__activity_digests')) {
            $schema->createTable('#__activity_digests')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('scope', 250)
                ->unsignedInteger('scope_id')->default(0)
                ->unsignedTinyInteger('frequency')->default(0)
                ->datetime('sent')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->index('idx_user_id', 'scope_id')
                ->index('idx_frequency', 'frequency')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__activity_digests');
    }
}
