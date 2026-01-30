<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Publications\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Members - Publications plugin
 **/
class Migration20181221000000PlgMembersPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('members', 'publications');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('members', 'publications');
    }
}
