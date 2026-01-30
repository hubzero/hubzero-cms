<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Cart\Offline\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Cart - Offline plugin
 **/
class Migration20170831000000PlgCartOffline extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('cart', 'offline');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('cart', 'offline');
    }
}
