<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->deletePluginEntry('quickicon', 'extensionupdate');
        $this->deletePluginEntry('quickicon', 'joomlaupdate');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addPluginEntry('quickicon', 'extensionupdate');
        $this->addPluginEntry('quickicon', 'joomlaupdate');
    }
}
