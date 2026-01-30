<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Supergroup\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Supergroup plugin
 **/
class Migration20170831000000PlgSystemSupergroup extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'supergroup');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'supergroup');
    }
}
