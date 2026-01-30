<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Wishlist\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Publications - Wishlist plugin
 **/
class Migration20170831000000PlgPublicationsWishlist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('publications', 'wishlist');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('publications', 'wishlist');
    }
}
