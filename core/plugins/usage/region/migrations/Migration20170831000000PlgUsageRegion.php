<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Usage\Region\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Usage - Region plugin
 **/
class Migration20170831000000PlgUsageRegion extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('usage', 'region', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('usage', 'region');
    }
}
