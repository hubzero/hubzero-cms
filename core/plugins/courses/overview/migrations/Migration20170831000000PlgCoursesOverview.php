<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Overview\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Courses - Overview plugin
 **/
class Migration20170831000000PlgCoursesOverview extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('courses', 'overview');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('courses', 'overview');
    }
}
