<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for dropping enused courses tables
 *
*/
class Migration20131021090512ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__courses_inviteemails');
        $schema->dropTable('#__courses_events');

        if ($schema->tableExists('#__courses_enrollments') && $this->db->getDatabase() != 'nanohub') {
            $schema->dropTable('#__courses_enrollments');
        }

        $schema->dropTable('#__courses_email');
        $schema->dropTable('#__courses_email_log');
        $schema->dropTable('#__courses_email_version');
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__courses_email')) {
            $schema->createTable('#__courses_email')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('offering_id')->default(0)
                ->string('name', 255)->default('')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_email_log')) {
            $schema->createTable('#__courses_email_log')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('email_id')->default(0)
                ->integer('version_id')->default(0)
                ->string('to', 100)->default('')
                ->datetime('sent')->default('0000-00-00 00:00:00')
                ->integer('sent_by')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_email_version')) {
            $schema->createTable('#__courses_email_version')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('email_id')->default(0)
                ->string('subject', 255)->default('')
                ->text('body')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_enrollments')) {
            $schema->createTable('#__courses_enrollments')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('offering_id')->default(0)
                ->integer('user_id')->default(0)
                ->integer('enrollment_id')->default(0)
                ->string('status', 100)->default('')
                ->string('fname', 200)->default('')
                ->string('lname', 200)->default('')
                ->string('email1', 100)->default('')
                ->string('email2', 100)->default('')
                ->string('hubaccount', 100)->default('')
                ->datetime('date')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_events')) {
            $schema->createTable('#__courses_events')
                ->integer('id', ['autoIncrement' => true])
                ->integer('gidNumber')
                ->integer('actorid')
                ->string('title', 255)
                ->text('details')
                ->string('type', 50)
                ->datetime('start')
                ->datetime('end')
                ->tinyInteger('active')
                ->datetime('created')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_inviteemails')) {
            $schema->createTable('#__courses_inviteemails')
                ->integer('id', ['autoIncrement' => true])
                ->string('email', 150)
                ->integer('gidNumber')
                ->string('token', 255)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
