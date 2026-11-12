<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Members\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Members module
 *
*/
class Migration20190109000000ModMembers extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_members', 1, '', 1);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_members');
    }
}
