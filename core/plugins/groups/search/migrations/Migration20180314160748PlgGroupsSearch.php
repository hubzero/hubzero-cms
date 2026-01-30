<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Groups\Search\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Groups - Search plugin
**/
class Migration20180314160748PlgGroupsSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('groups', 'search');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('groups', 'search');
    }
}
