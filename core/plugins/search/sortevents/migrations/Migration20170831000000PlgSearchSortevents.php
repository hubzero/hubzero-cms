<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Sortevents\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - Sortevents plugin
 **/
class Migration20170831000000PlgSearchSortevents extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'sortevents');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'sortevents');
    }
}
