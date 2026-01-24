<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

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
