<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Handlers\Hubpresenter\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Handlers - Hubpresenter plugin
 **/
class Migration20170831000000PlgHandlersHubpresenter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'hubpresenter');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'hubpresenter');
    }
}
