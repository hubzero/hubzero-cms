<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Wishlists\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - Wishlists plugin
 **/
class Migration20170831000000PlgSearchWishlists extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'wishlists');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'wishlists');
    }
}
