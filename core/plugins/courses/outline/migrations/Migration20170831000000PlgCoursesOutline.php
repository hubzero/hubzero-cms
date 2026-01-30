<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Outline\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Courses - Outline plugin
 **/
class Migration20170831000000PlgCoursesOutline extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('courses', 'outline');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('courses', 'outline');
    }
}
