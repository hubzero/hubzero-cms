<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Handlers\Script\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Handlers - Scripts plugin
 **/
class Migration20190213000000PlgHandlersScript extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'script');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'script');
    }
}
