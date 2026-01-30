<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Memberoptions\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Groups - Memberoptions plugin
**/
class Migration20170831000000PlgGroupsMemberoptions extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('groups', 'memberoptions');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('groups', 'memberoptions');
    }
}
