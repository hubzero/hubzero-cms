<?php

namespace Plugins\Projects\Notes\Migrations;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
/**
 * Migration script for adding entry for Projects - Notes plugin
 **/
class Migration20170831000000PlgProjectsNotes extends \Hubzero\Content\Migration\Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('projects', 'notes');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('projects', 'notes');
    }
}
