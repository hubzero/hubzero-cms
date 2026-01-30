<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Handlers\Ipynb\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Handlers - Jupyter Notebook plugin
 **/
class Migration20190213000000PlgHandlersIpynb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'ipynb');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'ipynb');
    }
}
