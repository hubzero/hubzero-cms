<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Handlers\Video\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Handlers - Video plugin
 **/
class Migration20190213000000PlgHandlersVideo extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'video');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'video');
    }
}
