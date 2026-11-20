<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Quickicon\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing quickicon module
 *
*/
class Migration20190109000000ModQuickicon extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_quickicon', 1, '', 1);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_quickicon');
    }
}
