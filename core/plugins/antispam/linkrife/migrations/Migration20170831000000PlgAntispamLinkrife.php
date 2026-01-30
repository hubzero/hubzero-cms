<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Linkrife\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Antispam - Linkrife plugin
 **/
class Migration20170831000000PlgAntispamLinkrife extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('antispam', 'linkrife');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('antispam', 'linkrife');
    }
}
