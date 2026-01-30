<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Windowstools\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Windowstools plugin
 **/
class Migration20170831000000PlgResourcesWindowstools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'windowstools');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'windowstools');
    }
}
