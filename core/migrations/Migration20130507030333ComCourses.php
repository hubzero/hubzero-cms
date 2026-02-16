<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding courses badge implementation
 *
*/
class Migration20130507030333ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__courses_offering_badges')) {
            $schema->createTable('#__courses_offering_badges')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('offering_id')
                ->integer('badge_id')
                ->string('img_url', 255)->default('')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->hasColumn('#__courses_offerings', 'badge_id')) {
            $schema->addColumn('#__courses_offerings', 'badge_id')->integer()->nullable()->default(null)->execute();
        }

        if (!$schema->tableExists('#__courses_member_badges')) {
            $schema->createTable('#__courses_member_badges')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('member_id')
                ->integer('earned')->nullable()
                ->datetime('earned_on')->nullable()
                ->string('claim_url', 255)->nullable()
                ->integer('claimed')->nullable()
                ->datetime('claimed_on')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('member_id', 'member_id')
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

        $schema->dropTable('#__courses_offering_badges');

        if ($schema->hasColumn('#__courses_offerings', 'badge_id')) {
            $schema->dropColumn('#__courses_offerings', 'badge_id');
        }

        $schema->dropTable('#__courses_member_badges');
    }
}
