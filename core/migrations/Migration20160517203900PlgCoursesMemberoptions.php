<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing unused plg_courses_memberoptions plugin
 *
*/
class Migration20160517203900PlgCoursesMemberoptions extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('courses', 'memberoptions');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('courses', 'memberoptions');
    }
}
