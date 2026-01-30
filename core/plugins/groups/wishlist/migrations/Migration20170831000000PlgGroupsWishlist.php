<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Wishlist\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Groups - Wishlist plugin
**/
class Migration20170831000000PlgGroupsWishlist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('groups', 'wishlist');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('groups', 'wishlist');
    }
}
