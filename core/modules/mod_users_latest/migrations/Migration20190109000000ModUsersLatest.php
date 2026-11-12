<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\UsersLatest\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing users_latest module
 *
*/
class Migration20190109000000ModUsersLatest extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_users_latest', 1, '', 1);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_users_latest');
    }
}
