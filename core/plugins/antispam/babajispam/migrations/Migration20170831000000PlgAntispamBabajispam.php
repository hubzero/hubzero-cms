<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Babajispam\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Antispam - Babajispam plugin
 **/
class Migration20170831000000PlgAntispamBabajispam extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('antispam', 'babajispam');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('antispam', 'babajispam');
    }
}
