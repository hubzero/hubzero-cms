<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding rate limiting table
  *
**/
class Migration20150623152144ComDeveloper extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__developer_rate_limit')) {
            $schema->createTable('#__developer_rate_limit')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('application_id')->nullable()
                ->integer('uidNumber')->nullable()
                ->string('ip', 255)->nullable()
                ->integer('limit_short')->nullable()
                ->integer('limit_long')->nullable()
                ->integer('count_short')->nullable()
                ->integer('count_long')->nullable()
                ->datetime('expires_short')->nullable()
                ->datetime('expires_long')->nullable()
                ->datetime('created')->nullable()
                ->primaryKey('id')
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

        $schema->dropTable('#__developer_rate_limit');
    }
}
