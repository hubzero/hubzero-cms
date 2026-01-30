<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding resources cron plugin
 *
*/
class Migration20150312140000PlgCronResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('cron', 'resources');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('cron', 'resources');
    }
}
