<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Search\Wiki\Migrations;

use Hubzero\Content\Migration\Base;

/**
*
 * Migration script for adding entry for Search - Wiki plugin
 **/
class Migration20170831000000PlgSearchWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'wiki');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'wiki');
    }
}
