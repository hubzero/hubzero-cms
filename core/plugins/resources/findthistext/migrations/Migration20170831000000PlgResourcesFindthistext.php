<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Findthistext\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Findthistext plugin
 **/
class Migration20170831000000PlgResourcesFindthistext extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'findthistext', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'findthistext');
    }
}
