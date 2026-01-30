<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\System\Authfactors\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Authfactors plugin
 **/
class Migration20170831000000PlgSystemAuthfactors extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'authfactors', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'authfactors');
    }
}
