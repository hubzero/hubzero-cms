<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Blacklist\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Antispam - Blacklist plugin
 **/
class Migration20170831000000PlgAntispamBlacklist extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('antispam', 'blacklist');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('antispam', 'blacklist');
    }
}
