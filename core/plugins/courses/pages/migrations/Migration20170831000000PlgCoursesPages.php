<?php

// @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Courses - Pages plugin
 **/
class Migration20170831000000PlgCoursesPages extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('courses', 'pages');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('courses', 'pages');
    }
}
