<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for for adding wikiwyg plugin
**/
class Migration20140211083120PlgEditorsWikiwyg extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('editors', 'wikiwyg');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('editors', 'wikiwyg');
    }
}
