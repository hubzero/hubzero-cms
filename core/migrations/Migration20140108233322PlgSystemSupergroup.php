<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding super group system plugin
**/
class Migration20140108233322PlgSystemSupergroup extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'supergroup');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'supergroup');
    }
}
