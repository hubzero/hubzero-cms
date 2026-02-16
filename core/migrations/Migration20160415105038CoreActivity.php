<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding activity log tables
 *
*/
class Migration20160415105038CoreActivity extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__activity_logs')) {
            $schema->createTable('#__activity_logs')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->unsignedInteger('created_by')->default(0)
                ->string('description', 250)->nullable()
                ->string('action', 100)->nullable()
                ->string('scope', 250)->default('')
                ->unsignedInteger('scope_id')->default(0)
                ->text('details')->nullable()
                ->primaryKey('id')
                ->index('idx_created_by', 'created_by')
                ->index('idx_scope_scope_id', ['scope', 'scope_id'])
                ->index('idx_action', 'action')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__activity_recipients')) {
            $schema->createTable('#__activity_recipients')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('log_id')->default(0)
                ->string('scope', 250)
                ->unsignedInteger('scope_id')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->datetime('viewed')->default('0000-00-00 00:00:00')
                ->tinyInteger('state')->default(0)
                ->primaryKey('id')
                ->index('idx_log_id', 'log_id')
                ->index('idx_user_id', 'scope_id')
                ->index('idx_state', 'state')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__activity_subscriptions')) {
            $schema->createTable('#__activity_subscriptions')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('user_id')->default(0)
                ->string('scope', 250)->default('')
                ->unsignedInteger('scope_id')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->index('idx_user_id', 'user_id')
                ->index('idx_scope_scope_id', ['scope', 'scope_id'])
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

        if ($schema->tableExists('#__activity_logs')) {
            $schema->dropTable('#__activity_logs');
        }

        if ($schema->tableExists('#__activity_recipients')) {
            $schema->dropTable('#__activity_recipients');
        }

        if ($schema->tableExists('#__activity_subscriptions')) {
            $schema->dropTable('#__activity_subscriptions');
        }
    }
}
