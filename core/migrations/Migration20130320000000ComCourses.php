<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding courses gradebook table
  *
**/
class Migration20130320000000ComCourses extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__courses_grade_book')) {
            $schema->createTable('#__courses_grade_book')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')
                ->decimal('score', 5, 2)->default('0.00')
                ->string('scope', 255)->default('asset')
                ->integer('scope_id')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__courses_grade_book');
    }
}
