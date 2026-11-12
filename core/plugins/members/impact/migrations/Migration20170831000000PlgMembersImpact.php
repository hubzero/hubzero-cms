<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Impact\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Impact plugin
 **/
class Migration20170831000000PlgMembersImpact extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'impact');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'impact');
    }
}
