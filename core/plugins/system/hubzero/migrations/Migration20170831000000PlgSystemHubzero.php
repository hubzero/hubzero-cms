<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Hubzero\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Hubzero plugin
 **/
class Migration20170831000000PlgSystemHubzero extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'hubzero');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'hubzero');
    }
}
