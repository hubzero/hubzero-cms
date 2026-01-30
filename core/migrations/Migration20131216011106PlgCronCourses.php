<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding courses cron plugin
 *
*/
class Migration20131216011106PlgCronCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'courses');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'courses');
    }
}
