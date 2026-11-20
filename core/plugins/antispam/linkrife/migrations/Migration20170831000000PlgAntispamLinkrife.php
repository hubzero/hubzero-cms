<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Linkrife\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Antispam - Linkrife plugin
 **/
class Migration20170831000000PlgAntispamLinkrife extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('antispam', 'linkrife');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('antispam', 'linkrife');
    }
}
