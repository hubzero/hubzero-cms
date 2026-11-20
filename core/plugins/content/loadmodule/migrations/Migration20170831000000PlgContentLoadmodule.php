<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Loadmodule\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Content - Loadmodule plugin
 *
**/
class Migration20170831000000PlgContentLoadmodule extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'loadmodule', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'loadmodule');
    }
}
