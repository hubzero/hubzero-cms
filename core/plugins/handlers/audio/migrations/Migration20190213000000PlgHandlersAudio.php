<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Handlers\Audio\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Handlers -Audio plugin
 **/
class Migration20190213000000PlgHandlersAudio extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'audio');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'audio');
    }
}
