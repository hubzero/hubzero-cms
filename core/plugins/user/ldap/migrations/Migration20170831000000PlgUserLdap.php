<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Ldap\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding User - LDAP plugin
 **/
class Migration20170831000000PlgUserLdap extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('user', 'ldap');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('user', 'ldap');
    }
}
