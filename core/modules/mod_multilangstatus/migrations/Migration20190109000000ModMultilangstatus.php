<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Multilangstatus\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Multilangstatus module
 *
*/
class Migration20190109000000ModMultilangstatus extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_multilangstatus');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_multilangstatus');
    }
}
