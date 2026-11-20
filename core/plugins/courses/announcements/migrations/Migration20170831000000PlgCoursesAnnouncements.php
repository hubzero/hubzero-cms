<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Announcements\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Courses - Announcements plugin
 **/
class Migration20170831000000PlgCoursesAnnouncements extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('courses', 'announcements');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('courses', 'announcements');
    }
}
