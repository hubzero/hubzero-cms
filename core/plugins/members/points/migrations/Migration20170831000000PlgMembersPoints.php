<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Points\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Points plugin
 **/
class Migration20170831000000PlgMembersPoints extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'points');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'points');
    }
}
