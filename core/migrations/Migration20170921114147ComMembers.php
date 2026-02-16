<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__users_log_auth table
  *
**/
class Migration20170921114147ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__users_log_auth')) {
            $schema->addIndex('#__users_log_auth', 'idx_username', 'username');

            $schema->addIndex('#__users_log_auth', 'idx_ip', 'ip');

            $schema->addIndex('#__users_log_auth', 'idx_status', 'status');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__users_log_auth')) {
            $schema->dropIndex('#__users_log_auth', 'idx_username');

            $schema->dropIndex('#__users_log_auth', 'idx_ip');

            $schema->dropIndex('#__users_log_auth', 'idx_status');
        }
    }
}
