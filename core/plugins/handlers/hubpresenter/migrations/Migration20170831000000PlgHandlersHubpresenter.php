<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'hubpresenter');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'hubpresenter');
    }
}
