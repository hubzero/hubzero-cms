<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Notes\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Courses - Notes plugin
 **/
class Migration20170831000000PlgCoursesNotes extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('courses', 'notes');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('courses', 'notes');
    }
}
