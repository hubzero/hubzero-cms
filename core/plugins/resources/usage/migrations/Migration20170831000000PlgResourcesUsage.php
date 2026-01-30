<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Usage\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Usage plugin
 **/
class Migration20170831000000PlgResourcesUsage extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'usage');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'usage');
    }
}
