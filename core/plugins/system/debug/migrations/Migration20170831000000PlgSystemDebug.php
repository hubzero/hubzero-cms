<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Debug\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Debug plugin
 **/
class Migration20170831000000PlgSystemDebug extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'debug');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'debug');
    }
}
