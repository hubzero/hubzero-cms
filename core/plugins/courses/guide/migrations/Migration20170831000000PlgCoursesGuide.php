<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Guide\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Courses - Guide plugin
 **/
class Migration20170831000000PlgCoursesGuide extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('courses', 'guide');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('courses', 'guide');
    }
}
