<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Supporttickets\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing supporttickets module
 *
*/
class Migration20190109000000ModSupporttickets extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_supporttickets', 1, '', 1);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_supporttickets');
    }
}
