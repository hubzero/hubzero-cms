<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Announcements\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Groups - Announcements plugin
**/
class Migration20170831000000PlgGroupsAnnouncements extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('groups', 'announcements');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('groups', 'announcements');
    }
}
