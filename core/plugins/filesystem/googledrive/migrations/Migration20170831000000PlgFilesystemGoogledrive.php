<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Filesystem\Googledrive\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Filesystem - Googledrive plugin
 **/
class Migration20170831000000PlgFilesystemGoogledrive extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('filesystem', 'googledrive', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('filesystem', 'googledrive');
    }
}
