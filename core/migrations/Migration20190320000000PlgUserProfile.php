<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing User - Profile plugin
 *
*/
class Migration20190320000000PlgUserProfile extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deletePluginEntry('user', 'profile');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addPluginEntry('user', 'profile', 0);
    }
}
