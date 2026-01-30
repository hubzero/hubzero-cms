<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Memberhome\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Memberhome plugin
 **/
class Migration20170831000000PlgSystemMemberhome extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'memberhome', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'memberhome');
    }
}
