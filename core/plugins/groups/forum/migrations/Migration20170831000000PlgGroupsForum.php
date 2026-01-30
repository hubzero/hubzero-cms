<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Forum\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Groups - Forum plugin
**/
class Migration20170831000000PlgGroupsForum extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('groups', 'forum');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('groups', 'forum');
    }
}
