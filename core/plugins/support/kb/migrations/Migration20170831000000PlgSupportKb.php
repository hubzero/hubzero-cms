<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Kb\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Support - Kb plugin
 **/
class Migration20170831000000PlgSupportKb extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'kb');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'kb');
    }
}
