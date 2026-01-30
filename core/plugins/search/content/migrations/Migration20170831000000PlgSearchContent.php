<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Content\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - Content plugin
 **/
class Migration20170831000000PlgSearchContent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'content');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'content');
    }
}
