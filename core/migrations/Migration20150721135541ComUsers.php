<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating table #__users_log_auth
 *
*/
class Migration20150721135541ComUsers extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__users_log_auth')) {
            $schema
                ->createTable('#__users_log_auth')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')
                ->string('username', 150)->nullable()
                ->enum('status', ['success', 'failure'])->nullable()
                ->string('ip', 15)->nullable()
                ->datetime('logged')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__users_log_auth');
    }
}
