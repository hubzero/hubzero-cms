<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Xusers\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding User - Xusers plugin
 **/
class Migration20170831000000PlgUserXusers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('user', 'xusers');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('user', 'xusers');
    }
}
