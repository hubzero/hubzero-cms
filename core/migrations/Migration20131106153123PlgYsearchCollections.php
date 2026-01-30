<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding ysearch collection entry
 *
*/
class Migration20131106153123PlgYsearchCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('ysearch', 'collections');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('ysearch', 'collections');
    }
}
