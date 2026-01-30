<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Editors\Wikiwyg\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Editors - Wikiwyg plugin
 **/
class Migration20170831000000PlgEditorsWikiwyg extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('editors', 'wikiwyg', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('editors', 'wikiwyg');
    }
}
