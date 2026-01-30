<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Coins\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Coins plugin
 **/
class Migration20170831000000PlgResourcesCoins extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'coins');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'coins');
    }
}
