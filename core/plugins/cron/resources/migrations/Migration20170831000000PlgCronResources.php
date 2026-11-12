<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Resources\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Cron - Resources plugin
 **/
class Migration20170831000000PlgCronResources extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'resources');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'resources');
    }
}
