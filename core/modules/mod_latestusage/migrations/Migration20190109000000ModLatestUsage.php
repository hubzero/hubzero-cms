<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Latestusage\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Latest Usage module
 *
*/
class Migration20190109000000ModLatestUsage extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_latestusage');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_latestusage');
    }
}
