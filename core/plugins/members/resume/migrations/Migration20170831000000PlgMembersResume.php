<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Resume\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Resume plugin
 **/
class Migration20170831000000PlgMembersResume extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'resume');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'resume');
    }
}
