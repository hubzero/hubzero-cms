<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Collections\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Groups - Collections plugin
**/
class Migration20170831000000PlgGroupsCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('groups', 'collections');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('groups', 'collections');
    }
}
