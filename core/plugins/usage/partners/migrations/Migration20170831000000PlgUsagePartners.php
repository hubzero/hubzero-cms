<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Usage\Partners\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Usage - Partners plugin
 **/
class Migration20170831000000PlgUsagePartners extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('usage', 'partners', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('usage', 'partners');
    }
}
