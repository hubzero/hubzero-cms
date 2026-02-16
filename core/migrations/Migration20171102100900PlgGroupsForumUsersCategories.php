<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to create the `#__forum_users_categories` table
 *
*/
class Migration20171102100900PlgGroupsForumUsersCategories extends Base
{
    protected static $tableName = '#__forum_users_categories';

    public function up()
    {
        $schema = $this->db->schema();

        $tableName = self::$tableName;

        if (!$schema->tableExists($tableName)) {
            $schema->createTable($tableName)
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->unsignedInteger('user_id')
                ->unsignedInteger('category_id')
                ->timestamp('created')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        $tableName = self::$tableName;

        if ($schema->tableExists($tableName)) {
            $schema->dropTable($tableName);
        }
    }
}
