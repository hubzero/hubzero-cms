<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Pec\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Courses - PEC plugin
 **/
class Migration20170831000000PlgCoursesPec extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('courses', 'pec', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('courses', 'pec');
    }
}
