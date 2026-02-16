<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding unity asset table
 *
*/
class Migration20140212162812ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__courses_asset_unity')) {
            $schema->createTable('#__courses_asset_unity')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('member_id')
                ->integer('asset_id')
                ->datetime('created')
                ->tinyInteger('passed')
                ->text('details')->nullable()
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

        $schema->dropTable('#__courses_asset_unity');
    }
}
