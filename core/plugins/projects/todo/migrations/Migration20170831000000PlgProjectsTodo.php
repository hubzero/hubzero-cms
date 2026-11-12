<?php

namespace Plugins\Projects\Todo\Migrations;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
/**
 * Migration script for adding entry for Projects - To-do plugin
 **/
class Migration20170831000000PlgProjectsTodo extends \Hubzero\Content\Migration\Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('projects', 'todo');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('projects', 'todo');
    }
}
