<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing Quickicon plugins
 *
*/
class Migration20171109000000PlgQuickicon extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('quickicon', 'extensionupdate');
        $this->deletePluginEntry('quickicon', 'joomlaupdate');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('quickicon', 'extensionupdate');
        $this->addPluginEntry('quickicon', 'joomlaupdate');
    }
}
