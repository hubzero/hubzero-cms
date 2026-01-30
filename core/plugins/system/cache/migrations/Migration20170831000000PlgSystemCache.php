<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Cache\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Cache plugin
 **/
class Migration20170831000000PlgSystemCache extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'cache');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'cache');
    }
}
