<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Log\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Log plugin
 **/
class Migration20170831000000PlgSystemLog extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'log');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'log');
    }
}
