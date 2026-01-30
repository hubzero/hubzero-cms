<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Reviews\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Courses - Reviews plugin
 **/
class Migration20170831000000PlgCoursesReviews extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('courses', 'reviews');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('courses', 'reviews');
    }
}
