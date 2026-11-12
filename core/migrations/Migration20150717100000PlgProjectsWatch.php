<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding a Watch plugin for projects
  *
**/
class Migration20150717100000PlgProjectsWatch extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('projects', 'watch', 0);
    }

    /**
     * Up
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('projects', 'watch');
    }
}
