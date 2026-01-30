<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cron\Groups\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Cron - Groups plugin
 **/
class Migration20170831000000PlgCronGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'groups');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'groups');
    }
}
