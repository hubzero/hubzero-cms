<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Transfer\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Support - Transfer plugin
 **/
class Migration20170831000000PlgSupportTransfer extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'transfer', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'transfer');
    }
}
