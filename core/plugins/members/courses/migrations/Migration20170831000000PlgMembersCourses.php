<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Courses\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Courses plugin
 **/
class Migration20170831000000PlgMembersCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'courses');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'courses');
    }
}
