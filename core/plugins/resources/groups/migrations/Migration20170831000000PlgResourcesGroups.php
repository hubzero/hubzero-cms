<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Groups\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Groups plugin
 **/
class Migration20170831000000PlgResourcesGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'groups');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'groups');
    }
}
