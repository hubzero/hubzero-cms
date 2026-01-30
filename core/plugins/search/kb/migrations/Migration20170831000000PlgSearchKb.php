<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Kb\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - Kb plugin
 **/
class Migration20170831000000PlgSearchKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'kb');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'kb');
    }
}
