<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Support\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Cron - Support plugin
 **/
class Migration20170831000000PlgCronSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'support');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'support');
    }
}
