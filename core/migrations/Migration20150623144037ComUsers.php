<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for users reputation table
**/
class Migration20150623144037ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__user_reputation')) {
            $schema->createTable('#__user_reputation')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')->nullable()
                ->integer('spam_count')->default(0)
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

        if ($schema->tableExists('#__user_reputation')) {
            $schema->dropTable('#__user_reputation');
        }
    }
}
