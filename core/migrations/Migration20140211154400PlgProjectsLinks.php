<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding projects links plugin
 *
*/
class Migration20140211154400PlgProjectsLinks extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('projects', 'links');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('projects', 'links');
    }
}
