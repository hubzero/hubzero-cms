<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Autoapprove\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding User - Auto-approve plugin
 **/
class Migration20180831000000PlgUserAutoapprove extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('user', 'autoapprove', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('user', 'autoapprove');
    }
}
