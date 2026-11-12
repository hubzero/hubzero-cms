<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Resourcemenu\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing resourcemenu module
 *
*/
class Migration20190109000000ModResourcemenu extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_resourcemenu');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_resourcemenu');
    }
}
