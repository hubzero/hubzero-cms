<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding members impact plugin
 *
*/
class Migration20140529150000PlgMembersImpact extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'impact');
    }

    /**
     * Up
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'impact');
    }
}
