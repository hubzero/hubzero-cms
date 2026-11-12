<?php

namespace Plugins\Projects\Files\Migrations;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
/**
 * Migration script for adding entry for Projects - Files plugin
 **/
class Migration20170831000000PlgProjectsFiles extends \Hubzero\Content\Migration\Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('projects', 'files');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('projects', 'files');
    }
}
