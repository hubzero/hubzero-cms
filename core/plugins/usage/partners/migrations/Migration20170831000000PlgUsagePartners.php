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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('usage', 'partners', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('usage', 'partners');
    }
}
