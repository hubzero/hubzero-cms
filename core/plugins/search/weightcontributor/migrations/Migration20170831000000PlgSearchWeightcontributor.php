<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Weightcontributor\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - Weightcontributor plugin
 **/
class Migration20170831000000PlgSearchWeightcontributor extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'weightcontributor');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'weightcontributor');
    }
}
