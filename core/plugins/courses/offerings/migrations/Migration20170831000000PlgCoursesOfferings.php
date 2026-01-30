<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Offerings\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Courses - Offerings plugin
 **/
class Migration20170831000000PlgCoursesOfferings extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('courses', 'offerings');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('courses', 'offerings');
    }
}
