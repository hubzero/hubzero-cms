<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('cart', 'offline');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('cart', 'offline');
    }
}
