<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Activity\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Cron - Activity plugin
 **/
class Migration20170831000000PlgCronActivity extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'activity');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'activity');
    }
}
