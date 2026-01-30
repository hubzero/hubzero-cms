<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Mobile\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Mobile plugin
 **/
class Migration20170831000000PlgSystemMobile extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'mobile', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'mobile');
    }
}
