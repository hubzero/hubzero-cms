<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Categories\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Content - Categories plugin
 *
**/
class Migration20170831000000PlgContentCategories extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'categories');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'categories');
    }
}
