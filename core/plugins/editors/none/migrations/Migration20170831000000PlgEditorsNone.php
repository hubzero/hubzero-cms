<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Editors\None\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Editors - None plugin
 **/
class Migration20170831000000PlgEditorsNone extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('editors', 'none');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('editors', 'none');
    }
}
