<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to create the `#__support_criteria` table
 *
*/
class Migration20171115112800ComSupportCriteria extends Base
{
    /**
     * @var  string
     */
    protected static $tableName = '#__support_criteria';

    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $schema = $this->db->schema();

        $tableName = self::$tableName;

        if (!$schema->tableExists($tableName)) {
            $schema
                ->createTable($tableName)
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('description', 255)->nullable()
                ->string('query', 255)->nullable()
                ->timestamp('created')->nullable()
                ->timestamp('modified')->nullable()
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

        $tableName = self::$tableName;

        if ($schema->tableExists($tableName)) {
            $schema->dropTable($tableName);
        }
    }
}
