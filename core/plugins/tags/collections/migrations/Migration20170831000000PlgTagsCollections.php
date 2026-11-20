<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Tags\Collections\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Tags - Collections plugin
 **/
class Migration20170831000000PlgTagsCollections extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('tags', 'collections');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('tags', 'collections');
    }
}
