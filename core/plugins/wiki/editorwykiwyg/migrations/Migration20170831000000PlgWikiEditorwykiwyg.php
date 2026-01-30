<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Editorwykiwyg\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Wiki - Editorwykiwyg plugin
 **/
class Migration20170831000000PlgWikiEditorwykiwyg extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('wiki', 'editorwykiwyg');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('wiki', 'editorwykiwyg');
    }
}
