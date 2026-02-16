<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding more details to asset views table
**/
class Migration20130725184342ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__courses_asset_views')) {
            if (
                !$schema->hasColumn('#__courses_asset_views', 'course_id')
                && $schema->hasColumn('#__courses_asset_views', 'asset_id')
            ) {
                $schema->addColumn('#__courses_asset_views', 'course_id')->integer()->nullable()->execute();
            }
            if (
                !$schema->hasColumn('#__courses_asset_views', 'ip')
                && $schema->hasColumn('#__courses_asset_views', 'viewed_by')
            ) {
                $schema->addColumn('#__courses_asset_views', 'ip')->string(15)->nullable()->execute();
            }
            if (
                !$schema->hasColumn('#__courses_asset_views', 'url')
                && $schema->hasColumn('#__courses_asset_views', 'ip')
            ) {
                $schema->addColumn('#__courses_asset_views', 'url')->string(255)->nullable()->execute();
            }
            if (
                !$schema->hasColumn('#__courses_asset_views', 'referrer')
                && $schema->hasColumn('#__courses_asset_views', 'url')
            ) {
                $schema->addColumn('#__courses_asset_views', 'referrer')->string(255)->nullable()->execute();
            }
            if (
                !$schema->hasColumn('#__courses_asset_views', 'user_agent_string')
                && $schema->hasColumn('#__courses_asset_views', 'referrer')
            ) {
                $schema->addColumn('#__courses_asset_views', 'user_agent_string')->string(255)->nullable()->execute();
            }
            if (
                !$schema->hasColumn('#__courses_asset_views', 'session_id')
                && $schema->hasColumn('#__courses_asset_views', 'user_agent_string')
            ) {
                $schema->addColumn('#__courses_asset_views', 'session_id')->string(200)->nullable()->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_asset_views', 'course_id')) {
            $schema->dropColumn('#__courses_asset_views', 'course_id');
        }
        if ($schema->hasColumn('#__courses_asset_views', 'ip')) {
            $schema->dropColumn('#__courses_asset_views', 'ip');
        }
        if ($schema->hasColumn('#__courses_asset_views', 'url')) {
            $schema->dropColumn('#__courses_asset_views', 'url');
        }
        if ($schema->hasColumn('#__courses_asset_views', 'referrer')) {
            $schema->dropColumn('#__courses_asset_views', 'referrer');
        }
        if ($schema->hasColumn('#__courses_asset_views', 'user_agent_string')) {
            $schema->dropColumn('#__courses_asset_views', 'user_agent_string');
        }
        if ($schema->hasColumn('#__courses_asset_views', 'session_id')) {
            $schema->dropColumn('#__courses_asset_views', 'session_id');
        }
    }
}
