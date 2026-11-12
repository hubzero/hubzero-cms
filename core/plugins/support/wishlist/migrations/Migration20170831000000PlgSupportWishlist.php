<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Wishlist\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Support - Wishlist plugin
 **/
class Migration20170831000000PlgSupportWishlist extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'wishlist');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'wishlist');
    }
}
