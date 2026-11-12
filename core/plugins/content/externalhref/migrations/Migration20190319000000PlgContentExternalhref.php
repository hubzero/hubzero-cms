<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Externalhref\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Content - External HREF plugin
 *
**/
class Migration20190319000000PlgContentExternalhref extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'externalhref');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'externalhref');
    }
}
