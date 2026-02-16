<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__media_tracking_detailed table
 *
*/
class Migration20190228114147MediaTracking extends Base
{
    /**
     * List of tables
     *
     * @var  array
     **/
    public static $table = '#__media_tracking_detailed';

    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists(self::$table)) {
            $schema->addIndex(self::$table, 'idx_user_id', 'user_id');
            $schema->addIndex(self::$table, 'idx_session_id', 'session_id');
            $schema->addIndex(self::$table, 'idx_object_id', 'object_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists(self::$table)) {
            $schema->dropIndex(self::$table, 'idx_user_id');
            $schema->dropIndex(self::$table, 'idx_session_id');
            $schema->dropIndex(self::$table, 'idx_object_id');
        }
    }
}
