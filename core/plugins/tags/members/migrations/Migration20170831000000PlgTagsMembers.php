<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Tags\Members\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Tags - Members plugin
 **/
class Migration20170831000000PlgTagsMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('tags', 'members');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('tags', 'members');
    }
}
