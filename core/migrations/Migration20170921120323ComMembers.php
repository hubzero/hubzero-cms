<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__users tables
  *
**/
class Migration20170921120323ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_reputation')) {
            $schema->addIndex('#__user_reputation', 'idx_user_id', 'user_id');
        }

        if ($schema->tableExists('#__users_password_history')) {
            $schema->addIndex('#__users_password_history', 'idx_user_id', 'user_id');
        }

        if ($schema->tableExists('#__users_points')) {
            $schema->addIndex('#__users_points', 'idx_uid', 'uid');
        }

        if ($schema->tableExists('#__users_quotas')) {
            $schema->addIndex('#__users_quotas', 'idx_user_id', 'user_id');

            $schema->addIndex('#__users_quotas', 'idx_class_id', 'class_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__user_reputation')) {
            $schema->dropIndex('#__user_reputation', 'idx_user_id');
        }

        if ($schema->tableExists('#__users_password_history')) {
            $schema->dropIndex('#__users_password_history', 'idx_user_id');
        }

        if ($schema->tableExists('#__users_points')) {
            $schema->dropIndex('#__users_points', 'idx_uid');
        }

        if ($schema->tableExists('#__users_quotas')) {
            $schema->dropIndex('#__users_quotas', 'idx_user_id');

            $schema->dropIndex('#__users_quotas', 'idx_class_id');
        }
    }
}
