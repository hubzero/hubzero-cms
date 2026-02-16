<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing incorrect index on activity tables
 *
*/
class Migration20170913133847Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__activity_recipients')) {
            $schema->dropIndex('#__activity_recipients', 'idx_user_id');
            $schema->addIndex('#__activity_recipients', 'idx_scope_scope_id', ['scope', 'scope_id']);
        }

        if ($schema->tableExists('#__activity_digests')) {
            $schema->dropIndex('#__activity_digests', 'idx_user_id');
            $schema->addIndex('#__activity_digests', 'idx_scope_scope_id', ['scope', 'scope_id']);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__activity_recipients')) {
            $schema->dropIndex('#__activity_recipients', 'idx_scope_scope_id');
            $schema->addIndex('#__activity_recipients', 'idx_user_id', 'scope_id');
        }

        if ($schema->tableExists('#__activity_digests')) {
            $schema->dropIndex('#__activity_digests', 'idx_scope_scope_id');
            $schema->addIndex('#__activity_digests', 'idx_user_id', 'scope_id');
        }
    }
}
