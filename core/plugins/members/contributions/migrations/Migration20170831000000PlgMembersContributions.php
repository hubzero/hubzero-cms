<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Contributions\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Contributions plugin
 **/
class Migration20170831000000PlgMembersContributions extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'contributions');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'contributions');
    }
}
