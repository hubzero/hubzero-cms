<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Cache\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Cron - Cache plugin
 **/
class Migration20170831000000PlgCronCache extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'cache');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'cache');
    }
}
