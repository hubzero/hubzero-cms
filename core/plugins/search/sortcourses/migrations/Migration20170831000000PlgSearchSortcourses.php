<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Sortcourses\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - Sortcourses plugin
 **/
class Migration20170831000000PlgSearchSortcourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'sortcourses');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'sortcourses');
    }
}
