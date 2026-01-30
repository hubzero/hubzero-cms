<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Resources\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Support - Resources plugin
 **/
class Migration20170831000000PlgSupportResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'resources');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'resources');
    }
}
