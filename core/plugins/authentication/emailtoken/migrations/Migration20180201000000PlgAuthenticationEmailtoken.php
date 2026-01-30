<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authentication\Emailtoken\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Authentication - Email Token plugin
 **/
class Migration20180201000000PlgAuthenticationEmailtoken extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('authentication', 'emailtoken', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('authentication', 'emailtoken');
    }
}
