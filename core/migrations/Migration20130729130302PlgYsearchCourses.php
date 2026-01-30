<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding pages plugin entry
 *
*/
class Migration20130729130302PlgYsearchCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('ysearch', 'courses');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('ysearch', 'courses');
    }
}
